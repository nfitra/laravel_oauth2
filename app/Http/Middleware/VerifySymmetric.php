<?php

namespace App\Http\Middleware;

use Closure;
use DateTime;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Parser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response;

class VerifySymmetric
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->hasHeader('X-EXTERNAL-ID') || !$request->hasHeader('X-SIGNATURE') || !$request->hasHeader('X-TIMESTAMP')) {
            return response()->json([
                'responseCode' => '4007300',
                'responseMessage' => 'Invalid field format [externalId/signature/timestamp]',
            ], 400);
        }

        $externalId = $request->header('X-EXTERNAL-ID');
        $signature = $request->header('X-SIGNATURE');
        $timestamp = $request->header('X-TIMESTAMP');

        if (!$this->isISO8601Timestamp($timestamp)) {
            return response()->json([
                'responseCode' => '4007301',
                'responseMessage' => 'Invalid timestamp format [X-TIMESTAMP]',
            ], 400);
        }

        if (!$this->isValidExternalId($externalId)) {
            return response()->json([
                'responseCode' => '4007302',
                'responseMessage' => 'Invalid mandatory field [X-EXTERNAL-ID]',
            ], 400);
        }

        $secretKey = $this->getSecretKeyByExternalId();

        $method = strtoupper($request->method());
        $uri_encoded = $this->uriEncode($request->getRequestUri());
        $examined_body = $this->examineBody($request->getContent());
        $access_token = $request->bearerToken();

        $string2Sign = "$method:$uri_encoded:$access_token:$examined_body:$timestamp";
//        print_r([$secretKey, $uri_encoded, $examined_body, $access_token, $string2Sign]);die();

        if (!$this->verifyHmacSHA512($secretKey, $string2Sign, $signature)) {
            return response()->json([
                'responseCode' => '4017300',
                'responseMessage' => 'Unauthorized. [Signature]',
            ], 401);
        }

        if (!$this->isExistsExternalId($externalId)) {
            return response()->json([
                'responseCode' => '4017300',
                'responseMessage' => 'Unauthorized. [Unknown external id]',
            ], 401);
        }

        return $next($request);
    }

    private function isISO8601Timestamp($timestamp)
    {
        $dateTime = DateTime::createFromFormat(DateTime::ISO8601, $timestamp);
        return $dateTime instanceof DateTime;
    }

    private function isValidExternalId($externalId)
    {
        return strlen($externalId) > 36 || !is_string($externalId) ? 0 : 1;
    }

    private function isExistsExternalId($externalId)
    {
        return $externalId == '123' ? 1 : 0;
    }

    private function getSecretKeyByExternalId()
    {
        $client = auth('api')->client();
        return $client->secret;
    }

    private function verifyHmacSHA512($secretKey, $message, $expectedHash)
    {
        $hash = hash_hmac('sha512', $message, $secretKey, true);
        $base64EncodedHash = base64_encode($hash);
        return hash_equals($base64EncodedHash, $expectedHash);
    }

    private function uriEncode($url)
    {
        $parsed_url = parse_url($url);

        $path = isset($parsed_url['path']) ? $parsed_url['path'] : '/';
        $query = isset($parsed_url['query']) ? $parsed_url['query'] : '';

        $encoded_path = implode('/', array_map('rawurlencode', explode('/', $path)));

        $query_params = [];
        if (!empty($query)) {
            $pairs = explode('&', $query);
            foreach ($pairs as $pair) {
                $parts = explode('=', $pair, 2);
                $key = urldecode($parts[0]);
                $value = isset($parts[1]) ? urldecode($parts[1]) : null;

                if (isset($query_params[$key])) {
                    if (!is_array($query_params[$key])) {
                        $query_params[$key] = [$query_params[$key]];
                    }
                    $query_params[$key][] = $value;
                } else {
                    $query_params[$key] = $value;
                }
            }
        }

        foreach ($query_params as $param => &$value) {
            if (is_array($value)) {
                $value = implode('&' . $param . '=', $value);
            }
            $value = str_replace(['%2F', '%3F', '%3D', '%26'], ['/', '?', '=', '&'], rawurlencode(urldecode($value)));
        }

        $param_counts = array_count_values(array_keys($query_params));

        uksort($query_params, 'strcmp');
        foreach ($param_counts as $param => $count) {
            if ($count > 1) {
                uasort($query_params, function ($a, $b) {
                    $cmp = strcmp(urlencode($a), urlencode($b));
                    return ($cmp !== 0) ? $cmp : strcmp($a, $b);
                });
                break;
            }
        }

        $sorted_query = implode('&', array_map(
            function ($key, $value) {
                return $key . '=' . $value;
            },
            array_keys($query_params),
            $query_params
        ));

        $relative_url = $encoded_path . ($sorted_query ? '?' . $sorted_query : '');

        return $relative_url;
    }

    private function minifyJson($json)
    {
        $data = json_decode($json, true);
        $minified_json = $this->minifyJsonRecursive($data);
        return json_encode($minified_json, JSON_UNESCAPED_UNICODE);
    }

    private function minifyJsonRecursive($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $data[$key] = $this->minifyJsonRecursive($value);
                } elseif (is_string($value) && strpos($value, ' ') === false) {
                    $data[$key] = preg_replace('/\s+/', '', $value);
                }
            }
        }
        return $data;
    }

    private function examineBody($body)
    {
        $minified_json = !is_null($body) ? $this->minifyJson($body) : null;
        return strtolower(hash('SHA256', $minified_json));
    }
}
