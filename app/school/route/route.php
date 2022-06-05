<?php

use think\facade\Route;
use think\facade\Request;

$version = Request::header('version', 'v1'); #接口版本

Route::group('', function () use ($version) {
    /**
     * 登录
     * 注册
     * 提交申请
     * 获取用户信息
     */
    Route::group('auth', function () use ($version) {
        Route::post('login', "school/{$version}.Auth/userLogin");
        Route::post('reg', "school/{$version}.Auth/userRegister");
        Route::post('apply', "school/{$version}.Auth/userSchoolApply");
        Route::post('info', "school/{$version}.Auth/getSchoolInfo")->middleware('isLogin');
    });


})->allowCrossDomain([
    'Access-Control-Allow-Origin' => '*',
    'Access-Control-Allow-Methods' => 'GET,POST,OPTIONS,PUT',
    'Access-Control-Allow-Headers' => 'content-type,token,version',
    'Access-Control-Allow-Credentials' => 'true'
]);
