<?php

namespace App\Http\Controllers\OpenAPI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Laravel\Passport\Passport;

class AccessToken extends Controller
{
    public function b2b(Request $request)
    {
        $grantType = $request->input('grantType');

        if ($grantType === 'client_credentials') {
            $clientKey = $request->header('X-CLIENT-KEY');
            $clientSecret = Passport::client()->find($clientKey)->secret;

            $url = url('/openapi/v1.0/access-token/issue');
            $body = json_encode([
                'grant_type' => 'client_credentials',
                'client_id' => $clientKey,
                'client_secret' => $clientSecret,
            ]);
            $response = Http::timeout(10)->withBody($body)->post($url);
            if($response->status() != 100) {
                return $response;
            }

            $retrievedToken = json_decode($response->body());
            return response()->json([
                'responseCode' => '2007300',
                'responseMessage' => 'Successful',
                'accessToken' => $retrievedToken->access_token,
                'tokenType' => $retrievedToken->token_type,
                'tokenTimeout' => $retrievedToken->expires_in,
            ], 400);
        } else {
            return response()->json([
                'responseCode' => '200',
                'responseMessage' => 'Nothing in here',
            ], 400);
        }
    }
}
