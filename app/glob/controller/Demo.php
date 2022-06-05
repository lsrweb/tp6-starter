<?php

namespace app\glob\controller;

class Demo
{
    public function index()
    {
        return json(\app\common\model\Demo::select());
    }

    public function set()
    {
        $number = 100;
        $data = [
            'num' => $number
        ];
        (new \app\common\model\Demo)->save($data);

    }

    public function buy()
    {
        // number 依次 - 1
        $data = (new \app\common\model\Demo);

    }
}