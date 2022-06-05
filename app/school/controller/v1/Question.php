<?php

namespace app\school\controller\v1;

class Question extends BaseController
{
    // 题库列表
    public function questList(): \think\response\Json
    {
        $params = input('post.');
        $page = $params['page'] ?? 1;
        $pageSize = $params['pageSize'] ?? 10;
        $questList = \app\common\model\QuestionModel::where('status', 1)->page($page, $pageSize)->select();
        $questList = $questList->toArray();
        $questList = array_map(function ($item) {
            $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
            return $item;
        }, $questList);
        return successMsg('获取成功', $questList);
    }


}