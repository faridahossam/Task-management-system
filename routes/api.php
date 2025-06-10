<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');
Route::post('/login', 'App\Http\Controllers\AuthController@authenticate');

Route::group([
    'middleware' => ['auth:api'], //authentication check
    'namespace' => 'App\Http\Controllers\Api', // default namespace for api routes
], function () {
    //Manager Tasks routes
    Route::post('/tasks', 'Manager\TaskController@store');
    Route::patch('/tasks/{task}/update', 'Manager\TaskController@update');
    //User Tasks routes
    Route::get('/tasks', 'User\TaskController@index');
    Route::get('/tasks/{task}', 'User\TaskController@show');
    Route::patch('/tasks/{task}', 'User\TaskController@update');

    // get all statuses
    Route::get('/statuses', 'StatusController@index');
});
