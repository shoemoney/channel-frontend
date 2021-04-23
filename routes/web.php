<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\VideoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
| Any routes that require user sessions, should go into the "stateful"
| grouping of routes.
|
*/

Route::group(['middleware' => 'cacheResponse:600'], function () {
    Route::get('/video/{id}/{slug}', [VideoController::class, 'view']);
    Route::get('/container/{id}', [VideoController::class, 'container'])
        ->name('video.container');
});

Route::group(['middleware' => 'cacheResponse:3600'], function () {
    Route::get('/', [HomeController::class, 'index']);
    Route::get('/search', [SearchController::class, 'view']);
});

# Single action controllers do not work as advertised
# https://laravel.com/docs/8.x/controllers#single-action-controllers
Route::get('/images/d/{size}/{id}', '\App\Http\Controllers\ImageController')
    ->name('images');
