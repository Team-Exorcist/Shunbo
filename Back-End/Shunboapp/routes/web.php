<?php

use App\Http\Controllers\SslCommerzPaymentController;

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

// SSLCOMMERZ Start
Route::get('/example1', [SslCommerzPaymentController::class, 'exampleEasyCheckout']);
Route::get('/example2', [SslCommerzPaymentController::class, 'exampleHostedCheckout']);

Route::get('/pay', [SslCommerzPaymentController::class, 'index']);
Route::post('/pay-via-ajax', [SslCommerzPaymentController::class, 'payViaAjax']);

Route::get('/success', [SslCommerzPaymentController::class, 'success']);
Route::get('/fail', [SslCommerzPaymentController::class, 'fail']);
Route::get('/cancel', [SslCommerzPaymentController::class, 'cancel']);

Route::get('/ipn', [SslCommerzPaymentController::class, 'ipn']);
//SSLCOMMERZ END
