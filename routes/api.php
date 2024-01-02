<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'client'], function () {
    Route::get('/asymmetric', [\App\Http\Controllers\Client::class, 'asymmetric']);
    Route::get('/symmetric', [\App\Http\Controllers\Client::class, 'symmetric']);
});

Route::group(['middleware' => ['cors', 'json.response']], function () {
    Route::group(['prefix' => 'openapi/v1.0'], function () {

        Route::group(['middleware' => ['logs', 'asymmetric']], function () {
            Route::group(['prefix' => 'access-token'], function () {
                Route::post('/b2b', [\App\Http\Controllers\OpenAPI\AccessToken::class, 'b2b']);
            });
        });

        Route::group(['middleware' => ['logs', 'client', 'symmetric']], function () {
            Route::group(['prefix' => 'transfer-va'], function () {
                Route::get('/inquiry', [\App\Http\Controllers\OpenAPI\TransferVA::class, 'inquiry']);
                Route::get('/payment', [\App\Http\Controllers\OpenAPI\TransferVA::class, 'payment']);
            });
        });
    });
});
