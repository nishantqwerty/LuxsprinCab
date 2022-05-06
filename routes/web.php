<?php

use Illuminate\Support\Facades\Route;

include(app_path('/global_config.php'));
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::redirect('/api/reset-password/{token}', 'api/reset-password/{token}')->name('api/reset-password');
Route::group(['namespace' => 'Admin'], function () {


    Route::get('/admin', 'AuthController@login');
    Route::get('/admin/login', 'AuthController@login_post')->name('login');
    Route::post('/admin/login', 'AuthController@login_attempt')->name('login-attempt');
    Route::get('/admin/register', 'AuthController@register')->name('register');
    Route::post('/admin/register', 'AuthController@register_attempt')->name('register-attempt');
    Route::get('/admin/logout', 'AuthController@logout')->name('logout');
    //Password 
    Route::get('/admin/forget-password', 'AuthController@forgetPassword')->name('forget-password');
    Route::post('/admin/forget-password', 'AuthController@sendforgetPassword')->name('forgot-password');
    Route::get('/admin/reset-password/{string}', 'AuthController@resetPassword');
    Route::post('/admin/reset-password/{string}', 'AuthController@saveNewPassword')->name('reset-password');

    Route::group(['middleware' => 'auth', 'prefix' => '/admin'], function () {

        Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

        //Profile Controller
        Route::get('/profile', 'ProfileController@index')->name('profile');
        Route::post('/update-profile', 'ProfileController@updateProfile')->name('update-profile');
        Route::get('/change-password', 'ProfileController@changePassword')->name('change-password');
        Route::post('/change-password', 'ProfileController@savePassword')->name('save-password');

        //User Section Controller
        Route::group(['prefix' => '/users'], function () {
            Route::get('/', 'UserController@index')->name('users');
            Route::get('/edit/{id}', 'UserController@edit')->name('edit');
            Route::post('/edit/{id}', 'UserController@update')->name('update');
            Route::get('/view/{id}', 'UserController@view')->name('view');
            Route::get('/delete/{id}', 'UserController@delete')->name('delete');
            Route::get('/change-status/{id}/{status}', 'UserController@changeStatus')->name('change-status');
        });

        //Driver Section Controller
        Route::group(['prefix' => '/drivers'], function () {
            Route::get('/', 'DriverController@index')->name('drivers');
            Route::get('/edit/{id}', 'DriverController@edit')->name('edit-driver');
            Route::post('/edit/{id}', 'DriverController@update')->name('update-driver');
            Route::get('/view/{id}', 'DriverController@view')->name('view-driver');
            Route::get('/delete/{id}', 'DriverController@delete')->name('delete-driver');
            Route::get('/change-status/{id}/{status}', 'DriverController@changeStatus')->name('change-driver-status');
            Route::get('/accept-reject/{id}/{status}', 'DriverController@acceptReject')->name('accept-reject');

            Route::get('/view-documents/{id}', 'DriverController@viewDocuments')->name('view-documents');
            Route::get('/reject-documents/{id}/{status}', 'DriverController@rejectDocuments')->name('reject');
            Route::post('/save-reject-documents/{id}', 'DriverController@saveRejectDocuments')->name('/save-reject');
        });

        //sub-admin
        Route::group(['prefix' => '/profile'], function () {
            Route::get('/delete-image/{id}', 'ProfileController@deleteImage')->name('delete-image');
        });

        Route::group(['prefix' => '/car-fare'], function () {
            Route::get('/', 'CarsController@index')->name('car-fare');
            Route::get('/add-fare', 'CarsController@add')->name('add-fare');
            Route::post('/add-fare', 'CarsController@save')->name('save-fare');
            Route::get('/edit-fare/{id}', 'CarsController@edit')->name('edit-fare');
            Route::post('/edit-fare/{id}', 'CarsController@update')->name('update-fare');
            Route::get('/delete/{id}', 'CarsController@delete')->name('delete-fare');
        });
    });
});
