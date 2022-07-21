<?php

use Firebase\JWT\JWT;
use think\response\Json;
use think\facade\Env;

/**
 * @param string $msg
 * @param array $data
 * @param int $code
 */
function successMsg(string $msg = '', array $data = [], int $code = 1): Json
{
    return json(['code' => $code, 'message' => $msg, 'data' => $data]);
}

/**
 * @param string $msg '失败信息'
 * @param array $data '返回数据'
 * @param int $code '失败状态码'
 */
function errorMsg(string $msg = '', array $data = [], int $code = 0): Json
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

