<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VariableController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\AuthController;

Route::group(['middleware' => []], function () {
    Route::group(
        [
            'prefix' => 'auth'
        ],
        function () {
            Route::post('login', [AuthController::class, 'login']);
            Route::post('register', [AuthController::class, 'register']);
            Route::post('register-company', [AuthController::class, 'registerCompany']);

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

    Route::get('company-code/{joinableCode}', [CompanyController::class, 'getByCode']);

    Route::group(
        [
            'middleware' => 'auth:api'
        ],
        function () {
            Route::apiResource('variable', VariableController::class)->except('update', 'destroy');

            Route::group(
                [
                    'middleware' => 'role:employee'
                ],
                function () {
                    Route::get('personal-info', [PersonalController::class, 'getMyInfo']);
                    Route::post('personal-variable', [PersonalController::class, 'giveVariable']);
                    Route::post('personal', [PersonalController::class, 'updateMyInfo']);
                }
            );
        }
    );
});
