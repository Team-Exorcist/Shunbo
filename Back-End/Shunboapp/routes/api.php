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
    Route::get('/doc/getposts',[docController::class, 'getPosts']);
});


//public user
Route::get('/user/test',[userController::class,'test']);
Route::post('/user/register', [userController::class, 'register']);
Route::post('/user/login', [userController::class, 'login']);
Route::post('/user/verifymail', [userController::class, 'verifyMail']);
Route::post('/user/resetpassword', [userController::class, 'resetPassword']);
Route::post('/user/changepassword', [userController::class, 'changePassword']);


//protected user
Route::group(['middleware' => ['auth:sanctum', 'ability:user']], function(){
    Route::post('/user/logout', [userController::class, 'logout']);
    Route::post('/user/createpost', [userController::class, 'createPost']);
    Route::post('/user/makecomment', [userController::class, 'makeComment']);
    Route::post('/user/vote/{pid}',[userController::class, 'vote']);
    Route::post('/user/makeappointment', [userController::class, 'makeAppointment']);
    Route::get('/user/getposts',[userController::class, 'getPosts']);
    Route::get('/user/getcomments/{pid}',[userController::class, 'getComments']);
    Route::get('/user/getdoctorlist',[userController::class, 'getDoctorList']);
    Route::get('/user/getappointments/{uid}',[userController::class, 'getAppointments']);
});


