<?php

use cores\Request;

// 容器Provider定义文件
return [
    'think\Request' => Request::class,
    'think\exception\Handle' => \cores\exception\DebugException::class,
];
