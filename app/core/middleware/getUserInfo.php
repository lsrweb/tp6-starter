<?php


declare (strict_types=1);

namespace app\core\middleware;


class getUserInfo
{
    public function handle($request, \Closure $next)
    {
        $token = $request->header('Authorization');
        $res = checkToken($token);

        if ($res['code'] == -1) {
            return errorMsg($res['msg'], [], 401);
        }
        if ($res['code'] == -2) {
            return errorMsg($res['msg'], [], 508);
        }
        if ($res['data']->login_type != 'school') {
            return errorMsg('禁止访问', [], 403);
        }
        $request->user_id = $res['data']->uid;

        return $next($request);
    }
}
