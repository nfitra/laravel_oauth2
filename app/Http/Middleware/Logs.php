<?php

namespace App\Http\Middleware;

use App\Models\Log;
use Closure;
use DateTime;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Logs
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $datetime = new DateTime($request->header('X-TIMESTAMP'));

        $path = $request->path();
        if($path == 'openapi/v1.0/transfer-va/inquiry') {
            $module = 'va_inquiry';
        } else if ($path == 'openapi/v1.0/transfer-va/payment') {
            $module = 'va_payment';
        } else if ($path == 'openapi/v1.0/access-token/b2b') {
            $module = 'token_b2b';
        } else {
            $module = 'other';
        }

        $log = [
            'timestamp' => $datetime->format("Y-m-d H:i:s"),
            'ip' => $request->getClientIp(),
            'method' => $request->getMethod(),
            'uri' => $request->getRequestUri(),
            'module' => $module,
            'channel_id' => $request->header('CHANNEL-ID') ?: null,
            'partner_id' => $request->header('X-PARTNER-ID') ?: null,
            'external_id' => $request->header('X-EXTERNAL-ID') ?: null,
            'client_id' => isset(auth('api')->client()->id) ? auth('api')->client()->id : null,
            'request_header' => json_encode($request->headers->all()),
            'request_body' => json_encode($request->all()),
            'response' => $response->getContent(),
            'code' => $response->getStatusCode(),
        ];
        Log::insert($log);

        return $response;
    }
}
