<?php

use App\Http\Controllers\docController;
use App\Http\Controllers\userController;
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

//public doc
Route::get('/doc/test', [docController::class, 'test']);
Route::post('/doc/register',[docController::class, 'register']);
Route::post('/doc/login',[docController::class, 'login']);
Route::post('/doc/verifymail',[docController::class, 'verifyMail']);
Route::post('/doc/resetpassword',[docController::class, 'resetPassword']);
Route::post('/doc/changepassword', [docController::class, 'changePassword']);


//protected doctor
Route::group(['middleware' => ['auth:sanctum', 'ability:doctor']], function(){
    Route::post('/doc/logout', [docController::class, 'logout']);

    Route::post('/doc/createpost', [docController::class, 'createPost']);
    Route::post('/doc/makecomment', [docController::class, 'makeComment']);
    Route::post('/doc/vote/{pid}',[docController::class, 'vote']);

    Route::get('/doc/getfullpost',[docController::class, 'getFullPost']);
    Route::get('/doc/getposts',[docController::class, 'getPosts']);
    Route::get('/doc/getcomments/{pid}',[docController::class, 'getComments']);

    Route::get('/doc/getappointments/{did}',[docController::class, 'getAppointments']);

    Route::get('/doc/getdoctor/{did}',[docController::class, 'getDoctor']);

    Route::post('/doc/addmeetlink', [docController::class, 'addMeetLink']);

});


//public user
Route::get('/user/test',[userController::class,'test']);
Route::post('/user/register', [userController::class, 'register']);
Route::post('/user/login', [userController::class, 'login']);
Route::post('/user/verifymail', [userController::class, 'verifyMail']);
Route::post('/user/resetpassword', [userController::class, 'resetPassword']);
Route::post('/user/changepassword', [userController::class, 'changePassword']);



// SSLCOMMERZ Start
Route::get('/example1', [SslCommerzPaymentController::class, 'exampleEasyCheckout']);
Route::get('/example2', [SslCommerzPaymentController::class, 'exampleHostedCheckout']);

Route::post('/pay', [SslCommerzPaymentController::class, 'index']);
Route::post('/pay-via-ajax', [SslCommerzPaymentController::class, 'payViaAjax']);

Route::post('/success', [SslCommerzPaymentController::class, 'success']);
Route::post('/fail', [SslCommerzPaymentController::class, 'fail']);
Route::post('/cancel', [SslCommerzPaymentController::class, 'cancel']);

Route::post('/ipn', [SslCommerzPaymentController::class, 'ipn']);
//SSLCOMMERZ END



//protected user
Route::group(['middleware' => ['auth:sanctum', 'ability:user']], function(){
    Route::post('/user/logout', [userController::class, 'logout']);

    Route::post('/user/createpost', [userController::class, 'createPost']);
    Route::post('/user/makecomment', [userController::class, 'makeComment']);
    Route::post('/user/vote/{pid}',[userController::class, 'vote']);

    Route::post('/user/makeappointment', [userController::class, 'makeAppointment']);

    Route::get('/user/getfullpost',[userController::class, 'getFullPost']);
    Route::get('/user/getposts',[userController::class, 'getPosts']);
    Route::get('/user/getcomments/{pid}',[userController::class, 'getComments']);

    Route::get('/user/getdoctorlist',[userController::class, 'getDoctorList']);
    Route::get('/user/getappointments/{uid}',[userController::class, 'getAppointments']);

    Route::get('/user/getuser/{uid}',[userController::class, 'getUser']);

});


