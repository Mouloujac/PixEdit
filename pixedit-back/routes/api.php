<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;


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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});



Route::get('/storage/{file}', function ($file) {
    $image = Storage::get('http://localhost/storage/ui.png');
        return response($image, 200);
})->where('file', '.*');

Route::apiResource('image', App\Http\Controllers\ImageController::class);

Route::apiResource('album', App\Http\Controllers\AlbumController::class);


Route::get('/storage/{file}', 'App\Http\Controllers\ImageController@getImage')->name('image.get')->where('file', '.*');
