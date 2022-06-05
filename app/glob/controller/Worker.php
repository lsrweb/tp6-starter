<?php

namespace app\glob\controller;

use think\worker\Server;

class Worker extends Server
{
    protected $socket = 'http://0.0.0.0:2346';
    protected $daemonize = true;
    protected $processes = 4;

    // 发送信息
    public function send($connection, $data)
    {
        $connection->send('hello');
    }

    public function onMessage($connection, $data)
    {
        // 每隔1秒发送一次
        $connection->send(date('Y-m-d H:i:s'));
    }


}