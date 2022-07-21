<?php

use think\facade\Route;
use think\facade\Request;


Route::group('', function () {
    /**
     * 登录
     * 注册
     * 获取用户信息
     */
    Route::group('AuthModule', function () {
        Route::post('login', "base/AuthModule.indexAuth/AuthLogin");
        Route::post('register', "base/AuthModule.indexAuth/AuthRegister");
        Route::post('info', "school/Auth/getSchoolInfo")->middleware('isLogin');
    });


});