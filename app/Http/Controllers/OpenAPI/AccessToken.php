<?php

namespace App\Http\Controllers\OpenAPI;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Laravel\Passport\Passport;
use Laravel\Passport\Http\Controllers\AccessTokenController as ATCBase;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ServerRequestInterface;

class AccessToken extends ATCBase
{
    public function issueToken(ServerRequestInterface $request, $type = null)
    {
        if ($type == 'b2b') {
            $grantType = $request->getParsedBody()['grantType'];
            $clientKey = $request->getHeaderLine('X-CLIENT-KEY');
            $clientSecret = Passport::client()->find($clientKey)->secret;
            $body = ([
                'grant_type' => $grantType,
                'client_id' => $clientKey,
                'client_secret' => $clientSecret,
            ]);
            $requestModified = $request->withParsedBody($body);
            $tokenResponse = parent::issueToken($requestModified);

            $retrievedToken = json_decode($tokenResponse->getContent());
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

    public function b2b(ServerRequestInterface $request)
    {
        return $this->issueToken($request, 'b2b');
    }
}
