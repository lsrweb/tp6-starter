<?php

namespace app\school\controller\v1;

use app\glob\controller\Excel;
use app\school\validate\QuestValidate;
use think\exception\ValidateException;

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

    // 题库添加
    public function questAdd(): \think\response\Json
    {
        $params = input('post.');
        try {
            validate(QuestValidate::class)->scene('add')->check($params);


            return successMsg(1);

        } catch (ValidateException $e) {
            return errorMsg($e->getMessage());
        }
    }

    // 上传 xlx表格文件题库
    public function uploadXls(): \think\response\Json
    {
        $params = input('post.');
        try {
            validate(QuestValidate::class)->scene('add')->check($params);
            if (request()->file('file')) {
                $xlsResult = Excel::importExcel($this->request->file('file'));
            }
            return successMsg(1, $xlsResult);

        } catch (ValidateException $e) {
            return errorMsg($e->getMessage());
        }
    }


    // 题目禁用
    public function questDisable(): \think\response\Json
    {
        $params = input('post.disarray');
        $quest = \app\common\model\QuestionModel::where('id', $params['id'])->update(['status' => 0]);
        return successMsg('禁用成功');
    }

    // 删除题目
    public function questDelete(): \think\response\Json
    {
        $params = input('post.');
        $quest = \app\common\model\QuestionModel::where('id', $params['id'])->delete();
        return successMsg('删除成功');
    }


}