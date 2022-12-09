<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::group(['middleware' => []], function () {
    Route::group(
        [
            'prefix' => 'auth'
        ],
        function () {
            Route::post('login', [AuthController::class, 'login']);
            Route::post('register', [AuthController::class, 'register']);

            Route::group(
                [
                    'middleware' => 'auth:api'
                ],
                function () {
                    Route::post('logout', [AuthController::class, 'logout']);
                    Route::get('me', [AuthController::class, 'me']);
                }
            );
        }
    );
});
