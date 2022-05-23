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

Route::group(['namespace'   =>  'Api'], function () {

    Route::post('/login', 'LoginController@login');
    Route::post('/register', 'LoginController@register');
    Route::post('/forgot-username', 'LoginController@forgotUsername');
    Route::post('/send-otp', 'LoginController@sendOtp');
    Route::post('/resend-otp', 'LoginController@resendOtp');
    Route::post('/verify-otp', 'LoginController@verifyOtp');
    Route::post('/reset-password', 'LoginController@resetPassword');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/dashboard', 'ProfileController@dashboard');
        Route::get('/logout', 'LoginController@logout');
        Route::get('/delete-account', 'LoginController@deleteAccount');
        Route::get('/profile', 'ProfileController@profile');
        Route::post('/update-profile', 'ProfileController@updateProfile');
        Route::post('/change-password', 'ProfileController@changePassword');

        Route::get('/location', 'LocationController@getLocation');
        Route::get('/driver-location', 'LocationController@GetDriverLocation');
        Route::post('/create-job', 'LocationController@createJob');
        Route::post('/create-booking', 'BookingController@createBooking');
        Route::post('/update-lat-long', 'BookingController@updateLatLong');

        Route::get('/home-screen', 'LocationController@GetDrivingDistance');
        Route::post('/save-booking', 'BookingController@saveBooking');
    });
});
