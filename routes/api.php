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

Route::get('/containers/get-optim', 'Api\PhotoContainersController@getOptimContainers');
Route::get('/containers/', 'Api\PhotoContainersController@index');
Route::get('/containers/{id}', 'Api\PhotoContainersController@show');
Route::delete('/containers/{id}', 'Api\PhotoContainersController@destroy');
Route::post('/containers/', 'Api\PhotoContainersController@store');
