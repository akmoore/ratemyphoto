<?php

use Illuminate\Http\Request;

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

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::post('email-only', 'AuthController@setTokenForEmailLogin');
    Route::post('validate-token', 'AuthController@validateToken');
    Route::post('profile', 'AuthController@profile');

});

Route::group(['middleware' => 'api'], function ($router) {
    Route::post('staff', 'StaffController@store');
    Route::get('staff', 'StaffController@index');
    Route::get('profile/{slug}', 'StaffController@show');
    Route::post('image', 'PhotoController@store');
    Route::get('images', 'PhotoController@index');
    Route::get('download/{id}', 'PhotoController@download');
    Route::post('prefer/{id}', 'PhotoController@prefer');
});
