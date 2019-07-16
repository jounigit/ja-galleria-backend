<?php

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
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
Route::get('del', function () {
    $del = Storage::deleteDirectory('public/7');
    return 'Poistettu' . $del;
});
Route::post('upload', function (Request $request) {
    // cache the uplaoded file
    $uploaded_file = $request->file('image');

    // generate a new filename. getClientOriginalExtension() for the file extension
    $filename = 'image-' . time() . '.' . $uploaded_file->getClientOriginalExtension();

    //Upload File
    $uploaded_file->storeAs('public/ja/images', $filename);
    $uploaded_file->storeAs('public/ja/thumbnails', $filename);


    $image_path = public_path('storage/ja/images/'.$filename);
    $thumbnail_path = public_path('storage/ja/thumbnails/'.$filename);
    //Resize image here
    $img = Image::make($image_path)->resize(500, 500, function($constraint) {
        $constraint->aspectRatio();
    });
    $imgthumb = Image::make($thumbnail_path)->resize(200, 200, function($constraint) {
        $constraint->aspectRatio();
    });
    $img->save($image_path);
    $imgthumb->save($thumbnail_path);

    dd($image_path . " - " . $thumbnail_path);
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('logout', 'Auth\LoginController@logout');
    Route::get('/users', 'UserController@index');
    Route::get('users/{user}', 'UserController@show');
    Route::resource('/albums', 'AlbumController')->except(['index', 'show']);
    Route::resource('/categories', 'CategoryController')->except(['index', 'show']);
    Route::resource('/pictures', 'PictureController')->except(['index', 'show']);
    // Route::delete('/pictures/{picture}', 'PictureController@destroy');
    // Route::post('/pictures','PictureController@store');

});
