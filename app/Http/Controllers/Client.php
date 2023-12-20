<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Client extends Controller
{
    public function index()
    {

    }

    public function asymmetric()
    {
        $url = url('/openapi/v1.0/access-token/b2b');
        $apiCredential = 'b66925de-d8ec-476e-a170-6cf06c863b78';

        $privateKey = <<<EOD
            -----BEGIN PRIVATE KEY-----
            MIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQC1q0icxg2vgljuXjpIPpUnWAL9NsRWtFMV4FFHrS2iC/1S7D8M1n1b9di7C5TYvNqcolItLhA06XMgm7VF61pZHi8CCiymWza9PvU79DfC9WNFdBpgSxai4dFCVxYAM8du3VcflT4qYPbQvHVYdyzCAEl2/6fecVskp3Jypvs
            ZvmaOs53PFdIpoEQIcF3YK+Xg4WGikNEugGnOFSgo60Wd96Js6Ro4QNdgmUPl7uvrHJ6bSncN/Ilwf1eLGL6bU04lgMFmHwKdIAaEQuZYLjMN1pNoCLDFFdJ/YAUoy03604V/IXNiKRgrR9uGSI8KYF7wJNn0y76c6X6Dpb8fa+/DAgMBAAECggEBAJf8hdJLS/XS2m4KPT5lxUlWM6H+qMJVON
            GrirSpqOzSlQxEA/fMlrJR+xF5ffzZ+xdiIdgUmpB54sycGEs3vK2kN/W/510CIMixHGAdUG11+KiJmuuGxphczkJvM0PWDfqtiQ8uQAUafENj99ScV8CylsPM3XeXZIZE5NYQ5zDAFVgY5a8DuhHaIJI19jdmP4nb1S7CFylRIQ8PGA+kU/hB5TUZXhtr6Iv2nRNcM01wev6AB8gz0qI9pOkLA
            gSIPnfBDl9gIpVWtt4tWedWoXR3W59KIgG1p6YIb/lDAeJSS+nYV4dbs+1zh5Lhl5WyanA1bqQoU1T+VEEx9xb8v/ECgYEA6nPkl7ezmjdrpin/vLQ8E9yyqI2sqw7kIv4AqLKiaKIS5pkZ/3jv9fLXNDlOz2MPPThefHWxv4Il/Cbf6JkCuJ71yajNCLw0OLiYJSYGC1Nuj4buG6oWOdI/xYAL
            eEfdvnu1atkgGMHx1xhreuspl3gGGIp85I6qf89/o8aMetsCgYEAxl2E5OUMnGORxshF/7l8FEw/bIxaQNLt2RkrOfDiDpSm3dBCASSnN2MDh9eTKTpy5KSnGX0uRVt/FRJ8XUTtVEQeSkD8QvraCFA9RPtNXlsQn7hhpOk1iVEHQB9xESdCya3B7ab42vzGBMXSwf8XQMcioa1JLmaYfFYc6E4
            hTzkCgYEAqV9aB+TVIhbhdOQodTm7oRmyE6Rt1hHm7ASVk0mhnHdhsiduqanDqOlrYLX54kaM7sw3LjCUXWZ3bIblARLw7VEg/TMuFB5ql4N7nnKusSXv3E48281vSwxBt7s+DgHVBtQ2Bl+fGWObA6oHk4ApxtwVg0sg2LjcIYNUkYtRVzsCgYAkVI55aaX0opvZX2bKnksmYIyhMdd51efwAh
            cTppWQfBNPvsvH79GcaEsGPypZu7W9QJbGKVInK8nLrzYN0wjwjQVLLjnFfrIeIawHDUuvQ1h5GEjx7jB69NcyHFAWBy3JSESjZRhg6zjNOPoPw8ubdp1WJSmpEOtOomrq9RxOqQKBgQDS2Z1PvHLaAzkWkKTwRMprbvYm/EYchvQRiY6OKWBVUEzuFfw2n/oI+yDr9PVwC2jjMMrsHXC86afxB
            y8n6LJ6Wf4DaZvigpbmDOxPGzBHExriuQKm6aQEOqKL4afiY7Ex1HdnlE3HYm9qp/MRgWaNtBb0P3d6BpRtDXeT0Flx+A==
            -----END PRIVATE KEY-----
            EOD;

        // '2017-03-17T09:44:18+07:00';
        $timestamp = time();
        $formattedTimestamp = date('c', $timestamp);

        $body = json_encode(array(
            'grantType' => 'client_credentials',
        ));

        $string2Sign = "$apiCredential|$formattedTimestamp";
        $securedHash = $this->SHA256withRSA($privateKey, $string2Sign);

        $headers = array(
            'Content-Type' => 'application/json',
            'X-CLIENT-KEY' => $apiCredential,
            'X-SIGNATURE' => $securedHash,
            'X-TIMESTAMP' => strval($formattedTimestamp),
        );

        return Http::timeout(5)->withHeaders($headers)->withBody($body)->post($url);
    }

    public function symmetric()
    {
        $url = '' ?: url('/openapi/inquery');
        $apiCredentialSecret = 'efc71ced-b0e7-4b47-8270-3c24829764aa';
        $accessToken = 'gp9HjjEj813Y9JGoqwOeOPWbnt4CUpvIJbU1mMU4a11MNDZ7Sg5u9a';

        // 2017-03-17T09:44:18+07:00
        $timestamp = time();
        $formattedTimestamp = '' ?: date('c', $timestamp);

        $body = '{"CorporateID":"H2HAUTO009","SourceAccountNumber":"0611104625","TransactionID":"00177914","TransactionDate":"2017-03-17","ReferenceID":"1234567890098765","CurrencyCode":"IDR","Amount":"175000000","BeneficiaryAccountNumber":"0613106704","Remark1":"Pencairan Kredit","Remark2":"1234567890098765"}';

        $uri_encoded = $this->uriEncode($url);
        $examined_body = $this->examineBody($body);
        $method = 'GET';

        $string2Sign = "$method:$uri_encoded:$accessToken:$examined_body:$formattedTimestamp";
        $securedHash = $this->hmacSHA512($apiCredentialSecret, $string2Sign);

        $headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer $accessToken",
            'X-EXTERNAL-ID' => '550e8400-e29b-41d4-a716-446655440000', // Unique reference number
            'X-SIGNATURE' => $securedHash,
            'X-TIMESTAMP' => strval($formattedTimestamp),
        );

        return Http::timeout(5)->withHeaders($headers)->withBody($body)->get($url);
    }

    private function SHA256withRSA($privateKey, $message)
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
