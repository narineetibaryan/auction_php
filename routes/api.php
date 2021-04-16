<?php

use Illuminate\Http\Request;
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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');

});

Route::group([
    'middleware' => ['api', 'auth']
], function ($router) {
    Route::post('products',             'ProductController@all');
    Route::get('products/{id}',         'ProductController@getOne');
    Route::post('products/bid',         'ProductController@bid');
    Route::get('products/autobid/{id}', 'ProductController@enableAutobid');

    Route::group([
        'middleware' => 'admin'
    ], function ($router) {
        Route::post('products/create',      'ProductController@create');
        Route::post('products/update/{id}', 'ProductController@update');
        Route::delete('products/{id}',      'ProductController@delete');
    });
});