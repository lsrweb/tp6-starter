<?php

use Firebase\JWT\JWT;
use think\response\Json;

/**
 * @param $msg 成功信息
 * @param $data 返回数据
 * @param $code 成功状态码
 */
function successMsg($msg = '', $data = [], $code = 1): Json
{
    return json(['code' => $code, 'message' => $msg, 'data' => $data]);
}

/**
 * @param $msg 失败信息
 * @param $data 返回数据
 * @param $code 失败状态码
 */
function errorMsg($msg = '', $data = [], $code = 0): Json
{
    return json(['code' => $code, 'message' => $msg, 'data' => $data]);
}

/**
 * @param $password
 * @return string
 */
function passwordEncrypt($password): string
{
    return md5(md5($password) . md5($password));
}

// 传入密码,判断密码是否正确
function passwordVerify($password, $passwordEncrypt): bool
{

    return passwordEncrypt($password) == $passwordEncrypt;
}


/**
 * 签发Toekn
 * @param $uid
 * @return string
 */
function signAccessToken($uid): string
{
    $key = config('jwt.jwt_key');
    $token = [
        'iss' => 'siriforever.ltd@gmail.com', //签发者 可选
        'aud' => '', //接收该JWT的一方，可选
        'iat' => time(), //签发时间
        'nbf' => time() + 10, //(Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
        'exp' => time() + 86400, //过期时间
        'data' => [
            'uid' => $uid,
            'login_type' => 'school'
        ],
    ];
    return JWT::encode($token, $key, 'HS256');
}


/**
 * 验证Token
 * @param $token
 * @return array
 */
function checkToken($token): array
{
    $key = config('jwt.jwt_key');
    $status = array("code" => -1);
    if (!$token) {
        return ['msg' => '用户验证错误', 'code' => -1];
    }
    try {
        JWT::$leeway = 60;//当前时间减去60，把时间留点余地
        $decoded = JWT::decode($token, new \Firebase\JWT\Key($key, 'HS256')); //HS256方式，这里要和签发的时候对应
        $arr = (array)$decoded;
        $res['code'] = 0;
        $res['data'] = $arr['data'];
        return $res;

    } catch (\Firebase\JWT\SignatureInvalidException $e) { //签名不正确
        $status['msg'] = "签名不正确";
        return $status;
    } catch (\Firebase\JWT\BeforeValidException $e) { // 签名在某个时间点之后才能用
        $status['msg'] = "token未生效";
        return $status;
    } catch (\Firebase\JWT\ExpiredException $e) { // token过期
        //return ["code" => -2, 'msg' => 'token失效'];
        $status['msg'] = "token失效";
        $status['code'] = -2;
        return $status;
    } catch (\Exception $e) { //其他错误
        dump($e);
        $status['msg'] = "未知错误";
        return $status;
    }
}