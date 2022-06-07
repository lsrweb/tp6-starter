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

    /**
     * 题库管理
     */
    Route::group('ques', function () use ($version) {
        Route::get('list', "school/{$version}.Question/questList");
        Route::post('create', "school/{$version}.Question/questAdd");
        Route::post('read-file', "school/{$version}.Question/uploadXls");

    });


});
