<?php

namespace app\core\controller\AuthModule;

use app\core\middleware\getUserInfo;
use cores\exception\DebugException;
use Firebase\JWT\JWT;

class indexAuth
{
    private $token;
    private $config;
    private $key;


    protected $middleware = [
        getUserInfo::class => ['only' => ['AuthLogin1']]
    ];

    // jwt TOKEN 签发
    public function __construct(array $data = [])
    {
        $this->init($data);
    }

    private function init($data = [])
    {
        $this->key = config('jwt.jwt_key');
        $this->config = [
            'iss' => config('jwt.jwt_iss'),
            'aud' => config('jwt.jwt_aud'),
            'iat' => time(),
            'nbf' => time() + 10,
            'exp' => time() + 86400,
            'data' => $data
        ];
        $this->sign();
    }

    // 签发
    private function sign()
    {
        $this->token = JWT::encode($this->config, $this->key, 'HS256');
    }

    // 解析
    private function decode(): \stdClass
    {
        return JWT::decode($this->token, $this->key, ['HS256']);
    }

    // 续签token-->暂定签发新token
    public function renewToken()
    {
        $this->init();
        return $this->token;
    }


    public function AuthLogin(): string
    {

        return $this->token;
    }

}