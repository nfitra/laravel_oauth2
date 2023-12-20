<?php

namespace App\Http\Middleware;

use Closure;
use DateTime;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyAsymmetric
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $grantTypeAllowed = ["client_credentials"];

        if (!$request->hasHeader('X-CLIENT-KEY') || !$request->hasHeader('X-SIGNATURE') || !$request->hasHeader('X-TIMESTAMP') || !in_array($request->input('grantType'), $grantTypeAllowed)) {
            return response()->json([
                'responseCode' => '4007300',
                'responseMessage' => 'Invalid field format [clientId/signature/timestamp/grantType]',
            ], 400);
        }

        $clientKey = $request->header('X-CLIENT-KEY');
        $signature = $request->header('X-SIGNATURE');
        $timestamp = $request->header('X-TIMESTAMP');

        if (!$this->isISO8601Timestamp($timestamp)) {
            return response()->json([
                'responseCode' => '4007301',
                'responseMessage' => 'Invalid timestamp format [X-TIMESTAMP]',
            ], 400);
        }

        if (!$this->isValidClientKey($clientKey)) {
            return response()->json([
                'responseCode' => '4007302',
                'responseMessage' => 'Invalid mandatory field [X-CLIENT-KEY]',
            ], 400);
        }

        if (!$this->isClientExists($clientKey)) {
            return response()->json([
                'responseCode' => '4017300',
                'responseMessage' => 'Unauthorized. [Unknown client]',
            ], 401);
        }

        $publicKey = <<<EOD
            -----BEGIN PUBLIC KEY-----
            MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtatInMYNr4JY7l46SD6VJ1gC/TbEVrRTFeBRR60togv9Uuw/DNZ9W/XYuwuU2LzanKJSLS4QNOlzIJu1RetaWR4vAgospls2vT71O/Q3wvVjRXQaYEsWouHRQlcWADPHbt1XH5U+KmD20Lx1WHcswgBJdv+n3nFbJKdycqb7Gb5mjrO
            dzxXSKaBECHBd2Cvl4OFhopDRLoBpzhUoKOtFnfeibOkaOEDXYJlD5e7r6xyem0p3DfyJcH9Xixi+m1NOJYDBZh8CnSAGhELmWC4zDdaTaAiwxRXSf2AFKMtN+tOFfyFzYikYK0fbhkiPCmBe8CTZ9Mu+nOl+g6W/H2vvwwIDAQAB
            -----END PUBLIC KEY-----
            EOD;

        $string2Sign = "$clientKey|$timestamp";

        if (!$this->verifySHA256withRSA($publicKey, $string2Sign, $signature)) {
            return response()->json([
                'responseCode' => '4017300',
                'responseMessage' => 'Unauthorized. [Signature]',
            ], 401);
        }

        return $next($request);
    }

    private function verifySHA256withRSA($publicKey, $message, $base64Signature)
    {
        $publicKeyResource = openssl_pkey_get_public($publicKey);

        if ($publicKeyResource === false) {
            die('Unable to load public key');
        }

        $signature = base64_decode($base64Signature);
        $verificationResult = openssl_verify($message, $signature, $publicKeyResource, OPENSSL_ALGO_SHA256);
        openssl_free_key($publicKeyResource);

        return $verificationResult === 1 ?: 0;
    }

    private function isISO8601Timestamp($timestamp)
    {
        $dateTime = DateTime::createFromFormat(DateTime::ISO8601, $timestamp);
        return $dateTime instanceof DateTime;
    }

    private function isUUID($string)
    {
        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';
        return preg_match($pattern, $string) === 1;
    }

    private function isValidClientKey($clientKey)
    {
        return $this->isUUID($clientKey);
    }

    private function isClientExists()
    {
        return true;
    }
}
