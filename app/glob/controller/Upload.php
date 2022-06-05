<?php

namespace app\glob\controller;

use Qiniu\Storage\UploadManager;
use think\facade\Env;
use Qiniu\Auth;
use think\facade\Filesystem;
use PHPExcel_IOFactory;

class Upload
{

    // 读取xls文档
    public function uploadXls()
    {
        // 接收文件上传信息
        $files = \request()->file("file");
        // 调用类库，读取excel中的内容
        $data = Excel::importExcel($files);
        // for循环 data 数据,过滤 null数据,分批次存储数据库
        $data = array_filter($data);
        foreach ($data as $key => $value) {
            // 先判断当前是否为空,如果为空则删除当前
            if (empty($value)) {
                unset($data[$key]);
                continue;
            }
            foreach ($value as $k => $v) {
                if (empty($v)) {
                    // 将下标1 key 值改为 title
                    $data[$key]['title'] = $v;
                    unset($value[$k]);
                    unset($value[0]);
                }
            }
            $score = array_pop($value);
            $value['score'] = $score;
            dump($value);


        }
    }

    protected function forEachData($data)
    {
        foreach ($data as $key => $value) {
            $data[$key] = $value;
        }
        return $data;
    }


    // 单图片上传至本地,字段名 file
    public function uploadLocal(): \think\response\Json
    {
        try {
            // 获取上传的文件，如果有上传错误，会抛出异常
            $file = \think\facade\Request::file('file');
            $type = \think\facade\Request::param('type', 'image');
            // 如果上传的文件为null，手动抛出一个异常，统一处理异常
            if (null === $file) {
                throw new \Exception('请上传文件', UPLOAD_ERR_NO_FILE);
            }
            // 使用验证器验证上传的文件
            validate(['file' => [
                // 限制文件大小(单位b)，这里限制为4M
                'fileSize' => 20 * 1024 * 1024,
                // 限制文件后缀，多个后缀以英文逗号分割
                'fileExt' => $type == 'image' ? 'jpg,png,gif,jpeg' : 'doc,docx,xls,xlsx,ppt,pptx,pdf,txt,zip,rar',
            ]])->check(['file' => $file]);
            $rule = 'md5';
            if ($type == 'image') {
                $path = \think\facade\Filesystem::putFile('upload', $file, $rule);
            } else {
                $path = \think\facade\Filesystem::putFile('file', $file, $rule);
            }
            // 返回url路径
            // 获取域名
            $domain = app()->request->domain();
            return successMsg('上传成功', ['url' => $domain . '/upload/' . $path]);
        } catch (\Exception $e) {
            // 捕获异常
            return errorMsg($e->getMessage(), []);
        }
    }


    // 七牛云上传
    public function uploadQN(): \think\response\Json
    {
        $file = request()->file('file');
        if (!$file) {
            return errorMsg('上传失败', [], 0);
        }

        $filePath = $file->getRealPath();

        $auth = new Auth(Env::get('GLOBAL_ENV.ACCESS_KEY'), Env::get('GLOBAL_ENV.SECRET_KEY'));
        $returnBody = '{"key":"$(key)","hash":"$(etag)","fsize":$(fsize),"name":"$(x:name)"}';
        $policy = array(
            'returnBody' => $returnBody
        );

        $uploadToken = $auth->uploadToken(Env::get('GLOBAL_ENV.BUCKET'), null, 7200, $policy, true);

        $uploadMgr = new UploadManager();

        $fileName = date('YmdHis') . mt_rand(1000, 9999) . '.' . $file->getOriginalName();
        try {
            list($ret, $err) = $uploadMgr->putFile($uploadToken, $fileName, $filePath);
            if ($err !== null) {
                return errorMsg('上传失败', $err, 0);
            } else {
                return successMsg('上传成功', [
                    'url' => Env::get('GLOBAL_ENV.IMAGE_URL') . '/' . $ret['key'],
                    'file_key' => $ret['key'],
                    'fileSize' => round($ret['fsize'] / 1024 / 1024, 2),
                ], 1);
            }
        } catch (\Exception $e) {
            return errorMsg('上传失败', [], 0);
        }
    }

    // 七牛云图片删除
    public function deleteQN(): \think\response\Json
    {
        $key = input('post.key');
        if (!$key) {
            return errorMsg('参数错误', [], 0);
        }
        $auth = new Auth(Env::get('GLOBAL_ENV.ACCESS_KEY'), Env::get('GLOBAL_ENV.SECRET_KEY'));
        $config = new \Qiniu\Config();
        $bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);
        list($ret, $err) = $bucketManager->delete(Env::get('GLOBAL_ENV.BUCKET'), $key);
        try {
            if ($err !== null) {
                // 手动抛出错误
                throw new \Exception($err->message());
            } else {
                return successMsg('删除成功', [], 1);
            }
        } catch (\Exception $e) {
            return errorMsg('删除失败 : ' . $e->getMessage(), [], 0);
        }
    }

    // 七牛云图片列表
    public function listQN(): \think\response\Json
    {
        $auth = new Auth(Env::get('GLOBAL_ENV.ACCESS_KEY'), Env::get('GLOBAL_ENV.SECRET_KEY'));
        $config = new \Qiniu\Config();
        $bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);
        $prefix = input('get.prefix', ''); // 前缀
        $marker = input('get.marker', '');
        $limit = input('get.limit', 10);
        list($result, $err) = $bucketManager->listFiles(Env::get('GLOBAL_ENV.BUCKET'), $prefix, $marker, $limit);

        try {
            if ($err !== null) {
                // 手动抛出错误
                throw new \Exception($err->message());
            } else {
                return successMsg('获取成功', $result, 1);
            }
        } catch (\Exception $e) {
            return errorMsg('获取失败 : ' . $e->getMessage(), [], 0);
        }
    }

    // 资源修改,重命名
    public function updateQN(): \think\response\Json
    {
        // 获取put方法传递的参数
        $key = input('get.key');
        $newKey = input('get.newKey');
        if (!$key || !$newKey) {
            return errorMsg('参数错误', [], 0);
        }

        $auth = new Auth(Env::get('GLOBAL_ENV.ACCESS_KEY'), Env::get('GLOBAL_ENV.SECRET_KEY'));
        $config = new \Qiniu\Config();
        $bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);

        list($ret, $err) = $bucketManager->rename(Env::get('GLOBAL_ENV.BUCKET'), $key, $newKey);
        try {
            if ($err !== null) {
                // 手动抛出错误
                throw new \Exception($err->message());
            } else {
                return successMsg('修改成功', [], 1);
            }
        } catch (\Exception $e) {
            return errorMsg('修改失败 : ' . $e->getMessage(), [], 0);
        }
    }


}