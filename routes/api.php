<?php

namespace App\Http\Controllers;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['auth:api']], function() {
    Route::group(['middleware' => ['active_user','user'],'prefix' => 'user'], function() {
        Route::get('/get-merchants', 'UserController@getMerchants');
    });

});

Route::post('/'.config('crudbooster.ADMIN_PATH').'/register', 'Auth\AuthController@register');
Route::group([ 'prefix' => 'auth'], function () {
    Route::get('user/verify/{token}', 'Auth\AuthController@verifyUser')->name('verify');
    Route::post('login', 'Auth\AuthController@login');
    
    
    Route::group([ 'middleware' => 'auth:api'], function() {
        Route::post('logout', 'Auth\AuthController@logout');
        Route::get('user', 'Auth\AuthController@user');
    });
});