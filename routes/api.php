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

Route::get('/video/{id}/{slug}', 'VideoController@viewJson');

Route::group(['middleware' => 'cacheResponse:60'], function () {
    Route::get('/suggestions', 'HomeController@suggestionsJson');
});

Route::group(['middleware' => 'cacheResponse:3600'], function () {
    Route::get('/', 'HomeController@indexJson');
    Route::get('/search', 'SearchController@viewJson');
});
