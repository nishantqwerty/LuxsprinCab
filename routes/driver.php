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
    Route::get('/temp-token', 'LoginController@tempToken');


    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/logout', 'LoginController@logout');
        Route::get('/delete-Account', 'LoginController@deleteAccount');
        Route::get('/online-offline/{id}', 'DashboardController@onlineOffline');
        Route::post('/bank-account', 'DashboardController@bankAccount');
        Route::get('/bank-account-detail', 'DashboardController@bankAccountDetail');
        Route::post('/documents', 'DashboardController@documents');
        Route::post('/update-documents', 'DashboardController@updateDocuments');
        Route::get('/vehicle-info', 'DashboardController@vehicleInfo');

        Route::get('/get-profile', 'ProfileController@getProfile');
        Route::post('/update-profile', 'ProfileController@updateProfile');
        Route::post('/change-password', 'ProfileController@changePassword');
        Route::post('/update-bank-account', 'DashboardController@updateBankAccount');
        Route::get('/cars', 'CarController@carDetails');
        Route::get('/car-models/{brand_id}', 'CarController@carModels');

        Route::get('/model-year', 'CarController@modelYear');
        Route::get('/category', 'CarController@carCategory');
        Route::get('/color', 'CarController@color');
        Route::get('/get-status', 'ProfileController@getStatus');
        Route::get('/update-location/{lat}/{long}', 'ProfileController@updateLocation');
        Route::post('/accept-reject', 'ProfileController@acceptReject');
        Route::get('/complete-booking/{booking_id}', 'ProfileController@acceptReject');
        Route::post('/cab-mode', 'CarController@cabMode');
        Route::get('/get-cab-mode', 'CarController@getCabMode');
    });
});
