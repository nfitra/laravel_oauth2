<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

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

//Route::group(['prefix' => 'auth', 'middleware' => ['cors', 'json.response']], function () {
//    Route::post('login', [AuthController::class, 'login'])->name('login');
//    Route::post('register', [AuthController::class, 'register']);
//
//    Route::group(['middleware' => 'auth:api'], function () {
//        Route::get('logout', [AuthController::class, 'logout']);
//        Route::get('user', [AuthController::class, 'user']);
//    });
//});


Route::group(['prefix' => 'openapi', 'middleware' => ['cors', 'json.response']], function () {

    Route::group(['prefix' => 'v1.0'], function () {
        Route::post('/access-token/b2b', [\App\Http\Controllers\OpenAPI\v1_0\AccessToken::class, 'b2b']);
    });

    Route::group(['middleware' => 'auth:api'], function () {
        Route::group(['middleware' => 'scope:test1'], function () {
            Route::get('inquery', [\App\Http\Controllers\Inquery::class, 'index']);
        });
    });
});
