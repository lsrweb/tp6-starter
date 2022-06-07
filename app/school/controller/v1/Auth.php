<?php

namespace app\school\controller\v1;

use app\common\model\SchoolUserModel;
use app\common\model\SchooModel;
use app\glob\controller\Email;
use app\school\validate\AuthValidate;
use think\exception\ValidateException;
use think\facade\Cache;
use think\Validate;

class Auth extends BaseController
{
    // 登录
    public function userLogin(): \think\response\Json
    {
        $params = input('post.');
        // 验证是否传入参数
        try {
            Validate([
                'username' => 'require',
                'password' => 'require',
                'code' => preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i", $params['username']) ? 'require' : '',
            ], [
                'username.require' => '请输入用户名',
                'password.require' => '请输入密码',
                'code.require' => '请输入邮箱验证码',
            ])->check($params);
            // 查询账号是否存在,判断传入account是账号还有邮箱
            // 判断字符是不是邮箱
            if (preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i", $params['username'])) {
                $user = SchoolUserModel::where('email', $params['username'])->find();
                $checkCode = Cache::store('redis')->get($params['username']);
                if (!$checkCode) {
                    return errorMsg('验证码已过期');
                }
                if ($checkCode != $params['code']) {
                    return errorMsg('邮箱验证码错误');
                }
                // 验证成功后删除缓存
                Cache::store('redis')->delete($params['username']);
            } else {
                $user = SchoolUserModel::where('phone', $params['username'])->find();
            }
            if (!$user) {
                return errorMsg('账号不存在');
            } else {
                // redis缓存
                $catch = Cache::store('redis')->get('school_' . $user['id']);
                // 验证缓存token是否正常未过期
                if (passwordVerify($params['password'], $user['password'])) {
                    // 如果不存在token则重新签发,存在则读取缓存token
                    if (!$catch) {
                        $token = signAccessToken($user['id']);
                        Cache::store('redis')->set('school_' . $user['id'], $token, 86400);
                    } else {
                        $token = $catch;
                    }
                    return successMsg('登录成功', ['access_token' => $token]);
                } else {
                    return errorMsg('密码错误');
                }
            }

        } catch (ValidateException $e) {
            return errorMsg($e->getMessage(), []);
        }


    }

    // 注册
    public function userRegister(): \think\response\Json
    {
        $params = input('post.');
        try {
            validate(AuthValidate::class)->scene('userRegister')->check($params);
            $SchoolUser = new SchoolUserModel();
            // 先查手机号,邮箱是否已存在,存在抛出,不存在执行注册
            $where = [
                'phone' => $params['phone'],
                'email' => $params['email'],
            ];
            $alreadyHas = $SchoolUser->where($where)->find();
            if ($alreadyHas) {
                // 查询school_id 是否已经审核完成,如果未审核则跳至下一步审核
                if ($alreadyHas['school_id'] == null && $alreadyHas['status'] == 1) {
                    return errorMsg('该账号已注册,请上传申请资料', [
                        'account_id' => $alreadyHas['id'],
                        'isCheck' => $alreadyHas['school_id']
                    ], 0);
                } else if ($alreadyHas['school_id'] == null && $alreadyHas['status'] == 2) {
                    return errorMsg('该账号已注册,请等待申请资料审核', [
                        'account_id' => $alreadyHas['id'],
                        'isCheck' => $alreadyHas['school_id']
                    ], 0);
                } else if ($alreadyHas['school_id'] != null && $alreadyHas['status'] == 3) {
                    return successMsg('审核成功!请用当前账号登录');
                } else {
                    return errorMsg('申请驳回!请查看驳回原因', ["cancel_result" => $alreadyHas['cancel_result']]);
                }
            } else {
                // 将字段名school_name 改为 驼峰,值不变
                $params['schoolName'] = $params['school_name'];
                unset($params['school_name']);
                $params['password'] = passwordEncrypt($params['password']);
                $saveResult = $SchoolUser->save($params);
                if ($saveResult) {
                    return successMsg('注册成功', [
                        'account_id' => $SchoolUser->id,
                    ], 1);
                } else {
                    return errorMsg('注册失败', [], 0);
                }
            }

        } catch (ValidateException $e) {
            return errorMsg($e->getError());
        }
    }

    // 提交申请
    public function userSchoolApply(): \think\response\Json
    {
        $params = input('post.');
        try {
            validate(AuthValidate::class)->scene('userSchoolApply')->check($params);
            $SchoolUser = new SchoolUserModel();
            $SchoolModel = new SchooModel();

            $user = $SchoolUser->where('id', $params['id'])->find();
            // 查询请勿重复提交
            if (!$user['school_id'] && $user['status'] == 2) {
                return errorMsg('材料已提交,请勿重复提交');
            }

            $response = $SchoolUser->where('id', $params['id'])->save([
                'status' => 2
            ]);
            $SchoolModel->save([
                'name' => $params['school_name'],
                'auditData' => $params['auditData'],
                'code' => $params['school_code']
            ]);

            return successMsg('申请提交成功', $response);
        } catch (ValidateException $e) {
            return errorMsg($e->getError());
        }
    }

    // 获取当前登录院校信息
    public function getSchoolInfo(): \think\response\Json
    {
        $schoolModel = new SchooModel();
        $infoData = $schoolModel->field('id,name,address,code,avatar')->where('id', $this->school_id)->find();
        return successMsg('获取成功', [
            'info_data' => $infoData
        ]);
    }

}