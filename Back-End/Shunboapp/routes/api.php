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
});

//public user
Route::get('/user/test',[userController::class,'test']);
Route::post('/user/register', [userController::class, 'register']);
Route::post('/user/login', [userController::class, 'login']);
Route::post('/user/verifymail', [userController::class, 'verifyMail']);
Route::post('/user/resetpassword', [userController::class, 'resetPassword']);
Route::post('/user/changepassword', [userController::class, 'changePassword']);


