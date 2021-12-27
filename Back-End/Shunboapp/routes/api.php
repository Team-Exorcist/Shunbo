<?php

use App\Http\Controllers\docController;
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


Route::get('/test', [docController::class, 'test']);
Route::post('/doc/register',[docController::class, 'register']);
Route::post('/doc/login',[docController::class, 'login']);


//protected

Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::post('/doc/logout', [docController::class, 'logout']);
});