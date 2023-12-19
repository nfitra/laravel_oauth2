<?php

namespace App\Http\Controllers\OpenAPI\v1_0;

use App\Http\Controllers\Controller;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Laravel\Passport\ClientRepository;

class AccessToken extends Controller
{
    public function b2b(Request $request)
    {
        $grantType = $request->input('grantType');

        if ($grantType === 'client_credentials') {
            if (!$request->hasHeader('X-CLIENT-KEY') || !$request->hasHeader('X-SIGNATURE') || !$request->hasHeader('X-TIMESTAMP')) {
                return response()->json([
                    'responseCode' => '4007300',
                    'responseMessage' => 'Invalid field format [clientId/clientSecret/grantType]',
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

            if (!$this->isClientKeyMatch()) {
                return response()->json([
                    'responseCode' => '4007302',
                    'responseMessage' => 'Invalid mandatory field [X-CLIENT-KEY]',
                ], 400);
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

            if (!$this->isClientExists($clientKey)) {
                return response()->json([
                    'responseCode' => '4017300',
                    'responseMessage' => 'Unauthorized. [Unknown client]',
                ], 401);
            }

            $clients = App::make(ClientRepository::class);

            $client = $clients->create(null, 'Test 123', '');
            print_r($client);
        } else {
            return response()->json([
                'responseCode' => '4007300',
                'responseMessage' => 'Invalid field format [clientId/clientSecret/grantType]',
            ], 400);
        }
    }

    private function checkGrantType(Request $request)
    {
        $creds = $request->input('grantType');
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

    private function isClientKeyMatch()
    {
        return true;
    }

    private function isClientExists()
    {
        return true;
    }
}
