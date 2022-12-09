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
        }
    );
});
