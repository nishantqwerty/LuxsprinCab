<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['namespace'   =>  'Driver'], function () {
    Route::post('/login', 'LoginController@login');
    Route::post('/register', 'LoginController@register');
    Route::post('/forgot-username', 'LoginController@forgotUsername');
    Route::post('/send-otp', 'LoginController@sendOtp');
    Route::post('/resend-otp', 'LoginController@resendOtp');
    Route::post('/verify-otp', 'LoginController@verifyOtp');
    Route::post('/reset-password', 'LoginController@resetPassword');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/online-offline/{id}','DashboardController@onlineOffline');
        Route::post('/bank-account','DashboardController@bankAccount');
    });
});
