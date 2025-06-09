<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');
Route::post('/login', 'App\Http\Controllers\AuthController@authenticate');

Route::group(['middleware' => ['auth:api']], function () {
    // Route::get('/tasks', 'App\Http\Controllers\Api\TaskController@index');
    // Route::post('/tasks', 'App\Http\Controllers\Api\TaskController@store');
    // Route::get('/tasks/{task}', 'App\Http\Controllers\Api\TaskController@show');
    // Route::put('/tasks/{task}', 'App\Http\Controllers\Api\TaskController@update');
    // Route::delete('/tasks/{task}', 'App\Http\Controllers\Api\TaskController@destroy');

    Route::get('/statuses', 'App\Http\Controllers\Api\StatusController@index');
});
