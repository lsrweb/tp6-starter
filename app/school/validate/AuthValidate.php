<?php

namespace app\school\validate;

class AuthValidate extends \think\Validate
{
    protected $rule = [
        'phone' => 'require|mobile',
        'email' => 'require|email',
        'school_name' => 'require',
        'password' => 'require|length:6,20',
        'auditData' => 'require',
        'school_code' => 'require',
    ];


    protected $message = [
        'phone.require' => '手机号不能为空',
        'phone.mobile' => '手机号格式不正确',
        'email.require' => '联系邮箱不能为空',
        'email.email' => '联系邮箱格式不正确',
        'school_name.require' => '学校名称不能为空',
        'password.require' => '请输入密码',
        'password.length' => '密码长度为6-20位',
        'auditData.require' => '请上传申请资料',
        'school_code' => '请输入学校代码',
    ];

    protected $scene = [
        'userLogin' => ['phone', 'password'],
        'userRegister' => ['phone', 'email', 'school_name', 'password'],
        'userSchoolApply' => ['name', 'address', 'school_code', 'auditData'],
    ];
}