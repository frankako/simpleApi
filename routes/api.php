<?php

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('users', 'UserController@index');
Route::post('users', 'UserController@store');
Route::get('users/{user}', 'UserController@show')->name('users.show');
Route::put('users/{user}', 'UserController@update');
Route::delete('users/{user}', 'UserController@destroy');
Route::get('users/{user}/notifications', 'UserController@notifications');
Route::get('users/{user}/unreadnotifications', 'UserController@unreadnotifications');

Route::post('oauth/token',
	'\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');

//Yelp routes
Route::get('yelps', 'YelpController@index');
