<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestUri
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $uri): Response
    {
        print_r([$request->back(), $uri]);die();
        if($request->getRequestUri() != $uri) {
            return response()->json([
                'responseCode' => '4007300',
                'responseMessage' => 'Connection not allowed',
            ], 400);
        }
        return $next($request);
    }
}
