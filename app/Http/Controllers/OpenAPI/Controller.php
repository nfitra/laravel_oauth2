<?php

namespace App\Http\Controllers\OpenAPI;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use function App\Helpers\getVirtualAccountValue;

class Controller extends BaseController
{
    public function sendResponse($message, $successData = [])
    {
        $response = [
            'responseCode' => 200,
            'responseMessage' => $message,
            'data' => $successData,
        ];

        return response()->json($response, 200);
    }

    public function sendError($message, $errorData = [], $code = 404)
    {
        $response = [
            'responseCode' => $code,
            'responseMessage' => $message,
        ];

        if (!empty($errorData)) {
            $response['data'] = $errorData;
        }

        return response()->json($response, $code);
    }

    public function sendServerError($code, $message)
    {
        $response = [
            'responseCode' => $code,
            'responseMessage' => $message,
            'virtualAccountData' => getVirtualAccountValue($message),
        ];

        return response()->json($response, 400);
    }
}
