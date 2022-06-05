<?php
declare (strict_types=1);

namespace app\school\middleware;


class isLogin
{
    public function handle($request, \Closure $next)
    {
        $token = $request->header('Authorization');
        $res = checkToken($token);
        if ($res['code'] == -1) {
            return errorMsg($res['msg'], 400);
        }
        if ($res['code'] == -2) {
            return errorMsg($res['msg'], 508);
        }
        if ($res['data']->login_type != 'school') {
            return errorMsg('禁止访问', 403);
        }
        $request->user_id = $res['data']->uid;
        $request->school_id = (new \app\common\model\SchoolUserModel)->where('id', $res['data']->uid)->value('school_id');

        return $next($request);
    }
}
