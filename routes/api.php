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
    /* 认证 */
    // 刷新token
    Route::put('/auth/current', 'AuthorizationsController@update')->name('api.auth.update');
    // 删除token
    Route::delete('/auth/current', 'AuthorizationsController@destroy')->name('api.auth.destroy');
    /* 用户 */
    // 获取当前登录用户信息
    Route::get('/user', 'UsersController@me');
    // 获取某用户信息 - 管理员权限
    Route::get('/users/{user}', 'UsersController@show');
    // 编辑用户信息
    Route::patch('/users/{user}', 'UsersController@update');
    /* 乘客 */
    // 获取当前登录用户的所有乘客
    Route::get('/passengers', 'PassengersController@index');
    // 获取某乘客信息
    Route::get('/passengers/{passenger}', 'PassengersController@show');
    // 添加乘客
    Route::post('/passengers', 'PassengersController@store');
    // 编辑乘客信息
    Route::patch('/passengers/{passenger}', 'PassengersController@update');
    // 删除乘客
    Route::delete('/passengers/{passenger}', 'PassengersController@delete');
    /* 车站 */
    // 查看车站列表
    Route::get('/stations', 'StationsController@index');
    // 搜索车站
    Route::post('/stations/search', 'StationsController@search');
    // 查看车站详情
    Route::get('/stations/{station}', 'StationsController@show');
    // 添加车站
    Route::post('/stations', 'StationsController@store');
    // 编辑车站
    Route::patch('/stations/{station}', 'StationsController@update');
    // 删除车站
    Route::delete('/stations/{station}', 'StationsController@delete');
    /* 列车 */
    // 查看列车列表
    Route::get('/trains', 'TrainsController@index');
    // 搜索列车
    Route::post('/trains/search', 'TrainsController@search');
    // 查看列车详情
    Route::get('/trains/{train}', 'TrainsController@show');
    // 添加列车
    Route::post('/trains', 'TrainsController@store');
    // 编辑列车
    Route::patch('/trains/{train}', 'TrainsController@update');
    // 删除列车
    Route::delete('/trains/{train}', 'TrainsController@delete');
});