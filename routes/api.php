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

Route::get('/containers/get-optim', 'Api\PhotoContainersController@getOptimContainers');
Route::get('/containers/', 'Api\PhotoContainersController@index');
Route::get('/containers/{id}', 'Api\PhotoContainersController@show');
Route::post('/containers/', 'Api\PhotoContainersController@store');
