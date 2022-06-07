<?php

use think\facade\Route;

Route::group('upload', function () {
    Route::post('file_local', 'glob/Upload/uploadLocal');
    Route::post('file_qiniu', 'glob/Upload/uploadQN');
    Route::post('file_delete', 'glob/Upload/deleteQN');
    Route::get('file_list', 'glob/Upload/listQN');
    Route::put('file_update', 'glob/Upload/updateQN');

    Route::post('upload_xls', 'glob/Upload/uploadXls');
});


Route::group('email', function () {
    Route::post('send', 'glob/Email/sendCodeToEmail');
});
