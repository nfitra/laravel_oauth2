<?php

namespace App\Http\Controllers\OpenAPI\v1_0;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;

class AccessToken extends Controller
{
    public function b2b(Request $request)
    {
        $grantType = $request->input('grantType');

        if ($grantType === 'client_credentials') {


            $clients = App::make(ClientRepository::class);

            $expireTime = now()->addMinutes(15);
            Passport::tokensExpireIn($expireTime);

            $clientKey = $request->header('X-CLIENT-KEY');
            $token = $clients->create(null, $clientKey, '');

            return response()->json([
                'responseCode' => '2007300',
                'responseMessage' => 'Successful',
                'accessToken' => $token->secret,
                'tokenType' => 'Bearer',
                'tokenTimeout' => $expireTime->diffInSeconds() + 1,
            ], 400);
        }
    }
}
