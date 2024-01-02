<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Client extends Controller
{
    public function asymmetric(Request $request)
    {
        $url = $request->input('url');
        $apiCredential = $request->input('api-credential');
        $privateKey = \Storage::disk('local')->get('private.pem');

        $timestamp = $request->input('timestamp') ?: now()->toIso8601String();

        $body = $request->getContent();

        $string2Sign = "$apiCredential|$timestamp";
        $securedHash = $this->sha256Rsa($privateKey, $string2Sign);

        $headers = array(
            'Content-Type' => 'application/json',
            'X-CLIENT-KEY' => $apiCredential,
            'X-SIGNATURE' => $securedHash,
            'X-TIMESTAMP' => strval($timestamp),
        );

        return Http::timeout(10)->withHeaders($headers)->withBody($body)->post($url);
    }

    public function symmetric(Request $request)
    {
        $url = $request->input('url');
        $apiCredentialSecret = $request->input('api-credential-secret');
        $accessToken = $request->input('access-token');
        $externalId = $request->input('external-id') ?: floor(microtime(true) * 1000) . mt_rand();

        $timestamp = $request->input('timestamp') ?: now()->toIso8601String();

        $body = $request->getContent();

        $uri_encoded = $this->uriEncode($url);
        $examined_body = $this->examineBody($body);
        $method = strtoupper("GET");

        $string2Sign = "$method:$uri_encoded:$accessToken:$examined_body:$timestamp";
        $securedHash = $this->hmacSHA512($apiCredentialSecret, $string2Sign);

        $headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer $accessToken",
            'X-EXTERNAL-ID' => $externalId,
            'X-SIGNATURE' => $securedHash,
            'X-TIMESTAMP' => strval($timestamp),
        );

        return Http::timeout(10)->withHeaders($headers)->withBody($body)->get($url);
    }

    private function sha256Rsa($privateKey, $message)
    {
        $privateKeyResource = openssl_pkey_get_private($privateKey);

        if ($privateKeyResource === false) {
            die('Unable to load private key');
        }

        $signature = null;
        openssl_sign($message, $signature, $privateKeyResource, OPENSSL_ALGO_SHA256);
        openssl_free_key($privateKeyResource);

        $base64Signature = base64_encode($signature);
        return $base64Signature;
    }

    function hmacSHA512($secretKey, $message)
    {
        $hash = hash_hmac('sha512', $message, $secretKey, true);
        $base64EncodedHash = base64_encode($hash);
        return $base64EncodedHash;
    }

    private function uriEncode($url)
    {
        $parsed_url = parse_url($url);

        $path = isset($parsed_url['path']) ? $parsed_url['path'] : '/';
        $query = isset($parsed_url['query']) ? $parsed_url['query'] : '';

        $encoded_path = implode('/', array_map('rawurlencode', explode('/', $path)));

        parse_str($query, $query_params);
        foreach ($query_params as $param => &$value) {
            $value = str_replace(['%2F', '%3F', '%3D', '%26'], ['/', '?', '=', '&'], rawurlencode(urldecode($value)));
        }

        $param_counts = array_count_values(array_keys($query_params));
        $duplicate_params = array_filter($param_counts, function ($count) {
            return $count > 1;
        });

        uksort($query_params, 'strcmp');

        foreach ($duplicate_params as $param => $count) {
            uasort($query_params, function ($a, $b) {
                $cmp = strcmp(urlencode($a), urlencode($b));
                return ($cmp !== 0) ? $cmp : strcmp($a, $b);
            });
            break;
        }

        $sorted_query = http_build_query($query_params, '', '&', PHP_QUERY_RFC3986);
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

    function examineBody($body)
    {
        $minified_json = !is_null($body) ? $this->minifyJson($body) : null;
        return strtolower(hash('SHA256', $minified_json));
    }
}
