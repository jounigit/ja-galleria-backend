<?php

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
// use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route;

// Tässä on vikaa
// use Illuminate\Routing\Route;
// Käytä tätä
// use Illuminate\Support\Facades\Route;

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

Route::post('login', 'Auth\LoginController@login');
Route::post('register', 'Auth\RegisterController@register');
Route::get('/albums', 'AlbumController@index');
Route::get('/albums/{album}', 'AlbumController@show');
Route::get('/categories', 'CategoryController@index');
Route::get('/categories/{category}', 'CategoryController@show');
Route::get('/pictures', 'PictureController@index');
Route::get('/pictures/{picture}', 'PictureController@show');

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('logout', 'Auth\LoginController@logout');
    Route::resource('/users', 'UserController');
    // Route::get('/users', 'UserController@index');
    // Route::get('users/{user}', 'UserController@show');
    Route::resource('/albums', 'AlbumController')->except(['index', 'show']);
    Route::resource('/categories', 'CategoryController')->except(['index', 'show']);
    Route::resource('/pictures', 'PictureController')->except(['index', 'show']);
    // Route::delete('/pictures/{picture}', 'PictureController@destroy');
    // Route::post('/pictures','PictureController@store');

});
