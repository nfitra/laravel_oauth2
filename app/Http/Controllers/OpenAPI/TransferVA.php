<?php

namespace App\Http\Controllers\OpenAPI;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TransferVA extends Controller
{
    public function inquiry(Request $request)
    {
        $url = 'http://10.10.10.96:88/inv-ifca/transfer_va/inquiry';
        $body = $request->getContent();

        try {
            $response = Http::timeout(10)->withBody($body)->post($url);
        } catch (ConnectionException $exception) {
            return $this->sendServerError(5042400, 'Timeout');
        }

        if ($response->serverError()) {
            return $this->sendServerError(4002400, 'Bad request');
        }

        return $response;
    }

    public function payment(Request $request)
    {
        $url = 'http://10.10.10.96:88/inv-ifca/transfer_va/payment';
        $body = $request->getContent();

        try {
            $response = Http::timeout(10)->withBody($body)->post($url);
        } catch (ConnectionException $exception) {
            return $this->sendServerError(5042500, 'Timeout');
        }

        if ($response->serverError()) {
            return $this->sendServerError(4002500, 'Bad request');
        }

        return $response;
    }
}
