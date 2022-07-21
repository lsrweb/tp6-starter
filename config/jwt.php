<?php

return [
    'jwt_key' => 'siriforever.ltd@gmail.com!@##%$^%^&*)*&###@!%!%!',
    // 过期时间为1天
    'jwt_expire_time' => 3600,
    // 签发者
    'jwt_iss' => '',
    // 接收方
    'jwt_aud' => '',
    // 签发时间
    'jwt_iat' => time(),
    // 过期时间
    'jwt_exp' => time() + 86400,
    // 面向的用户
    'jwt_data' => [],
];