<?php

namespace app\glob\controller;

use think\worker\Server;
use Workerman\Lib\Timer;

class Worker extends Server
{
    protected $socket = 'http://0.0.0.0:2346';
    protected $daemonize = true;

    // 暂放一侧
    public function onWorkerStart($worker)
    {
//        // 如果是第一个 worker process，则执行一些初始化工作
//        Timer::add(5, function () use ($worker) {
//            // 获取所有连接的客户端
//            $clients = $worker->connections;
//            // 获取每个客户端的ip
//            foreach ($clients as $client) {
//                // 获取连接参数 token
//
//            }
//
//
//            // 获取所有在线的客户端数量
//            $count = count($clients);
//
//
////            // 指定每个客户端发送的数据
////            foreach ($clients as $client) {
////                $client->send('当前在线人数：' . $count);
////            }
//
//
//        });
    }

    // 发送信息
    public function send($connection, $data)
    {
        // 获取当前连接的id
        $id = $connection->id;
        // 向客户端发送信息
        $connection->send($data);
    }

    public function onMessage($connection, $data)
    {
        // 获取连接id
        $connectionId = $connection->id;
//        // 发送给当前连接
//        $connection->send('hello');

    }


}