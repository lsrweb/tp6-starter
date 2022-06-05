<?php

return [
    // 扩展自身需要的配置
//    'protocol' => 'websocket', // 协议 支持 tcp udp unix http websocket text
//    'host' => '0.0.0.0', // 监听地址
//    'port' => 2345, // 监听端口
//    'socket' => '', // 完整监听地址
//    'context' => [], // socket 上下文选项
    'worker_class' => [
        \app\glob\controller\Worker::class

    ], // 自定义Workerman服务类名 支持数组定义多个服务
//
//    // 支持workerman的所有配置参数
//    'name' => 'thinkphp',
//    'count' => 4,
//    'daemonize' => false,
//    'pidFile' => '',
//
//    // 支持事件回调
//    // onWorkerStart
//    'onWorkerStart' => function ($worker) {
//
//    },
//    // onWorkerReload
//    'onWorkerReload' => function ($worker) {
//
//    },
//    // onConnect
//    'onConnect' => function ($connection) {
//    },
//    // onMessage
//    'onMessage' => function ($connection, $data) {
//
//    },
//    // onClose
//    'onClose' => function ($connection) {
//
//    },
//    // onError
//    'onError' => function ($connection, $code, $msg) {
//        echo "error [ $code ] $msg\n";
//    },
];
