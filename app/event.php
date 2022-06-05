<?php
// 事件定义文件
return [
    'bind' => [
    ],

    'listen' => [
        'AppInit' => [],
        'HttpRun' => [],
        'HttpEnd' => [],
        // 记录所有日志
        'LogLevel' => [
            'success'
        ],
        'LogWrite' => [],
    ],

    'subscribe' => [
    ],
];
