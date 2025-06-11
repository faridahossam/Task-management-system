<?php

use Illuminate\Support\Facades\Route;

//Login Route
Route::post('/login', 'App\Http\Controllers\AuthController@authenticate');

Route::group([
    'middleware' => ['auth:api'], //authentication check
    'namespace' => 'App\Http\Controllers\Api', // default namespace for api routes
], function () {
    // get all statuses
    Route::get('/statuses', 'StatusController@index');
    Route::ApiResource('roles', 'Admin\RolePermissionController');
});

//Manager Routes
Route::group([
    'prefix' => 'manager',
    'middleware' => ['auth:api', 'role:Manager'], //authentication check
    'namespace' => 'App\Http\Controllers\Api', // default namespace for api routes
], function () {
    //Tasks
    Route::post('/tasks', 'Manager\TaskController@store')->middleware('permission:Create Task');
    Route::patch('/tasks/{task}/update', 'Manager\TaskController@update')->middleware('permission:Manage Task Data');
    Route::get('/tasks', 'Manager\TaskController@index')->middleware('permission:View Tasks');
    Route::get('/tasks/{task}', 'Manager\TaskController@show')->middleware('permission:View Tasks');
    Route::patch('/tasks/{task}/status', 'Manager\TaskController@updateTaskStatus')->middleware('permission:Update Task Status');
});

//User Routes
Route::group([
    'prefix' => 'user',
    'middleware' => ['auth:api', 'role:User'], //authentication check
    'namespace' => 'App\Http\Controllers\Api', // default namespace for api routes
], function () {
    //Tasks
    Route::get('/tasks', 'User\TaskController@index')->middleware('permission:View Tasks');
    Route::get('/tasks/{task}', 'User\TaskController@show')->middleware('permission:View Tasks');
    Route::patch('/tasks/{task}/status', 'User\TaskController@update')->middleware('permission:Update Task Status');
});
