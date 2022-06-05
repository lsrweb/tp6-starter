<?php

use think\facade\Route;

Route::group('upload', function () {
    Route::post('file_local', 'glob/Upload/uploadLocal');
    Route::post('file_qiniu', 'glob/Upload/uploadQN');
    Route::post('file_delete', 'glob/Upload/deleteQN');
    Route::get('file_list', 'glob/Upload/listQN');
    Route::put('file_update', 'glob/Upload/updateQN');
    Route::post('upload_xls', 'glob/Upload/uploadXls');

})->allowCrossDomain([
    'Access-Control-Allow-Origin' => '*',
    'Access-Control-Allow-Methods' => 'GET,POST,OPTIONS,PUT',
    'Access-Control-Allow-Headers' => 'content-type,token,version',
    'Access-Control-Allow-Credentials' => 'true'
]);


Route::group('email', function () {
    Route::post('send', 'glob/Email/sendCodeToEmail');
})->allowCrossDomain([
    'Access-Control-Allow-Origin' => '*',
    'Access-Control-Allow-Methods' => 'GET,POST,OPTIONS,PUT',
    'Access-Control-Allow-Headers' => 'content-type,token,version',
    'Access-Control-Allow-Credentials' => 'true'
]);
