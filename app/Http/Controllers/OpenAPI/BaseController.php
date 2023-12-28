<?php

namespace App\Http\Controllers\OpenAPI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use function App\Helpers\vaData;

class BaseController extends Controller
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
            'virtualAccountData' => vaData($message),
        ];

        return response()->json($response, 400);
    }
}
