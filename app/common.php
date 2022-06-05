<?php

use Firebase\JWT\JWT;
use think\response\Json;
use think\facade\Env;

/**
 * @param $msg
 * @param $data
 * @param $code
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


/**
 * 生成密码hash值
 * @param string $password
 * @return string
 */
function encryption_hash(string $password): string
{
    return password_hash($password, PASSWORD_DEFAULT);
}


/**
 * 获取全局唯一标识符
 * @param bool $trim
 * @return string
 */
function get_guid_v4(bool $trim = true): string
{
    // Windows
    if (function_exists('com_create_guid') === true) {
        $charid = com_create_guid();
        return $trim == true ? trim($charid, '{}') : $charid;
    }
    // OSX/Linux
    if (function_exists('openssl_random_pseudo_bytes') === true) {
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    // Fallback (PHP 4.2+)
    mt_srand(intval((double)microtime() * 10000));
    $charid = strtolower(md5(uniqid((string)rand(), true)));
    $hyphen = chr(45);                  // "-"
    $lbrace = $trim ? "" : chr(123);    // "{"
    $rbrace = $trim ? "" : chr(125);    // "}"
    return $lbrace .
        substr($charid, 0, 8) . $hyphen .
        substr($charid, 8, 4) . $hyphen .
        substr($charid, 12, 4) . $hyphen .
        substr($charid, 16, 4) . $hyphen .
        substr($charid, 20, 12) .
        $rbrace;
}

/**
 * 时间戳转换日期
 * @param int|string $timeStamp 时间戳
 * @param bool $withTime 是否关联时间
 * @return false|string
 */
function format_time($timeStamp, bool $withTime = true)
{
    $format = 'Y-m-d';
    $withTime && $format .= ' H:i:s';
    return $timeStamp ? date($format, $timeStamp) : '';
}


/**
 * 过滤emoji表情
 * @param $text
 * @return null|string|string[]
 */
function filter_emoji($text)
{
    if (!is_string($text)) {
        return $text;
    }
    // 此处的preg_replace用于过滤emoji表情
    // 如需支持emoji表情, 需将mysql的编码改为utf8mb4
    return preg_replace('/[\xf0-\xf7].{3}/', '', $text);
}


/**
 * 当前是否为调试模式
 * @return bool
 */
function is_debug(): bool
{
    return (bool)Env::instance()->get('APP_DEBUG');
}

