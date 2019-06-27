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

Route::post('login', 'UserController@login');
Route::post('register', 'UserController@register');
Route::get('/albums','AlbumController@index');
Route::get('/albums/{album}','AlbumController@show');
Route::get('/pictures','PictureController@index');
Route::get('/pictures/{picture}','PictureController@show');

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/users','UserController@index');
    Route::get('users/{user}','UserController@show');
    Route::resource('/albums','AlbumController')->except(['index', 'show']);
    Route::resource('/pictures','PictureController')->except(['index', 'show']);
    // Route::delete('/pictures/{picture}', 'PictureController@destroy');
    // Route::post('/pictures','PictureController@store');

});

