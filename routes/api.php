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

Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome to CR Ticket API.',
        'status_code' => 200
    ]);
});

// 登录
Route::post('/auth', 'AuthorizationsController@store')->name('api.auth.store');

// 登陆后才可调用
Route::middleware('auth:api')->group(function() {
    // 刷新token
    Route::put('/auth/current', 'AuthorizationsController@update')->name('api.auth.update');
    // 删除token
    Route::delete('/auth/current', 'AuthorizationsController@destroy')->name('api.auth.destroy');
    // 获取当前登录用户信息
    Route::get('/user', 'UsersController@me');
    // 获取某用户信息 - 管理员权限
    Route::get('/users/{user}', 'UsersController@show');
    // 编辑用户信息
    Route::patch('/users/{user}', 'UsersController@update');
});