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
        Route::get('/cancel-booking/{booking_id}', 'BookingController@cancelBooking');

        Route::get('/getchat', 'ProfileController@getChat');
        Route::post('/send-chat',  'ProfileController@sendMessage');
        Route::get('/faqs', 'ProfileController@faqs');

        Route::post('/share-cab', 'BookingController@sharingCab');
        Route::post('/send-custom-notification', 'BookingController@sendCustomNotification');
        Route::get('/get-fare/{booking_id}', 'BookingController@getFare');

        Route::post('/submit-rating', 'ProfileController@submitRating');
        Route::get('/all-rating', 'ProfileController@showAllRating');
        Route::get('/booking-rating/{booking_id}', 'ProfileController@showBookingRating');

        Route::get('/completed-trips', 'BookingController@completedTrips');
        Route::get('/upcoming-trips', 'BookingController@upcomingTrips');
        Route::get('/ongoing-trips', 'BookingController@ongoingTrips');
    });
});
