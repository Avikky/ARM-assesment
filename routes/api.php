<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1'], function(){

    //employee endpoints
    Route::group(['prefix' => 'employee'], function(){

        Route::get('all', [\App\Http\Controllers\API\FetchController::class, 'fetchEmployees']);

        Route::post('register', [\App\Http\Controllers\API\EmployeeRegistration::class, 'register']);
        Route::post('verify-otp', [\App\Http\Controllers\API\EmployeeRegistration::class, 'verifyOtp']);

        Route::post('resend-otp', [\App\Http\Controllers\API\EmployeeRegistration::class, 'resendOtp']);

        Route::post('login', [\App\Http\Controllers\API\EmployeeRegistration::class, 'login']);

        Route::middleware('auth:api')->group( function () {
            Route::post('logout', [\App\Http\Controllers\API\EmployeeRegistration::class, 'logout']);
        });
    
    });

    //employer enpoints
    Route::group(['prefix' => 'employer'], function(){

        Route::get('all', [\App\Http\Controllers\API\FetchController::class, 'fetchEmployers']);

        Route::post('register', [\App\Http\Controllers\API\EmployerRegistration::class, 'register']);
        Route::post('verify-otp', [\App\Http\Controllers\API\EmployerRegistration::class, 'verifyOtp']);

        Route::post('resend-otp', [\App\Http\Controllers\API\EmployeeRegistration::class, 'resendOtp']);

        Route::post('login', [\App\Http\Controllers\API\EmployerRegistration::class, 'login']);

        
        Route::middleware('auth:api')->group( function () {
            Route::post('logout', [\App\Http\Controllers\API\EmployerRegistration::class, 'logout']);
            
            Route::post('remitance', [\App\Http\Controllers\API\Remitance::class, 'remitance']);
        });
    
    });



});

