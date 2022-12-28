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

    Route::get('/cancel','OrangeController@cancel');
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

        Route::group(['prefix' => '/route-stops'], function () {
            Route::get('/', 'RouteController@index')->name('route-stops');
            Route::get('/add-routes', 'RouteController@add')->name('add-routes');
            Route::post('/add-routes', 'RouteController@save')->name('save-routes');
            Route::get('/edit-routes/{id}', 'RouteController@edit')->name('edit-routes');
            Route::post('/edit-routes/{id}', 'RouteController@update')->name('update-routes');
            Route::get('/delete/{id}', 'RouteController@delete')->name('delete-routes');
        });

        Route::group(['prefix' => '/rating-messages'], function () {
            Route::get('/', 'RatingMessageController@index')->name('rating-messages');
            Route::get('/add-message', 'RatingMessageController@add')->name('add-route-message');
            Route::post('/add-message', 'RatingMessageController@save')->name('save-route-message');
            Route::get('/edit-message/{id}', 'RatingMessageController@edit')->name('edit-route-message');
            Route::post('/edit-message/{id}', 'RatingMessageController@update')->name('update-route-message');
            Route::get('/delete/{id}', 'RatingMessageController@delete')->name('delete-route-message');
        });

        Route::group(['prefix' => '/messages'], function () {
            Route::get('/', 'MessageController@index')->name('messages');
            Route::get('/show-chat', 'MessageController@show')->name('show-chat');
            Route::post('/save-chat', 'MessageController@saveChat')->name('save-chat');
        });

        Route::group(['prefix' => '/support'], function () {
            Route::get('/', 'SupportController@index')->name('support');
            Route::get('/add', 'SupportController@add')->name('add-faqs');
            Route::post('/add', 'SupportController@save')->name('save-faqs');
            Route::get('/edit/{id}', 'SupportController@edit')->name('edit-faqs');
            Route::post('/edit/{id}', 'SupportController@update')->name('update-faqs');
            Route::get('/delete/{id}', 'SupportController@delete')->name('delete-faq');
        });

        Route::group(['prefix' => '/promo'], function () {
            Route::get('/', 'PromoController@index')->name('promo');
            Route::get('/add', 'PromoController@add')->name('add-promo');
            Route::post('/add', 'PromoController@save')->name('save-promo');
            Route::get('/edit/{id}', 'PromoController@edit')->name('edit-faqs');
            Route::post('/edit/{id}', 'PromoController@update')->name('update-faqs');
            Route::get('/delete/{id}', 'PromoController@delete')->name('delete-faq');
        });

        Route::group(['prefix' => '/cancellation'], function () {
            Route::get('/', 'CancellationController@index')->name('cancellation');
            Route::get('/add', 'CancellationController@add')->name('add-message');
            Route::post('/add', 'CancellationController@save')->name('save-message');
            Route::get('/edit/{id}', 'CancellationController@edit')->name('edit-message');
            Route::post('/edit/{id}', 'CancellationController@update')->name('update-message');
            Route::get('/delete/{id}', 'CancellationController@delete')->name('delete-message');
        });

        Route::group(['prefix' => '/booking-reports'], function () {
            Route::get('/', 'BookingReportController@index')->name('booking-reports');
            Route::post('/', 'BookingReportController@date')->name('booking-reports-date');
        });

        Route::group(['prefix' => '/transaction-reports'], function () {
            Route::get('/', 'TransactionReportController@index')->name('transaction-reports');
            Route::post('/', 'TransactionReportController@date')->name('transaction-reports-date');
        });

        Route::group(['prefix' => '/panic'], function () {
            Route::get('/', 'PanicController@index')->name('panic');
        });

        Route::group(['prefix' => '/transaction'], function () {
            Route::get('/', 'TransactionController@index')->name('transaction');
            Route::get('/refund', 'TransactionController@refund')->name('refund');
        });

        Route::group(['prefix' => '/commission'], function () {
            Route::get('/', 'CommissionController@index')->name('commission');
            Route::post('/save', 'CommissionController@save')->name('save-percent');
        });
        
        Route::group(['prefix' => '/cancel-commission'], function () {
            Route::get('/', 'CancelCommissionController@index')->name('cancel-commission');
            Route::post('/save', 'CancelCommissionController@save')->name('save-percent');
        });

        Route::group(['prefix' => '/payout'], function () {
            Route::get('/', 'PayoutController@index')->name('payout');
            Route::post('/payment', 'PayoutController@stripe')->name('payment');
            // Route::post('/payment', 'PayoutController@stripe')->name('payment');

        });

        
    });
});
