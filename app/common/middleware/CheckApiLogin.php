<?php
declare (strict_types=1);

namespace app\common\middleware;

use app\common\model\Member;
use think\Response;

/**
 * 登录校验中间件
 * @author 牧羊人
 * @since 2021/1/8
 * Class CheckLogin
 * @package app\middleware
 */
class CheckApiLogin
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        // 获取Token
        $token = request()->header("token");
//        var_dump($token);
        if ($token != false) {
            // JWT解密token
            $jwt = new \Jwt();
            $jwt::$key = "show_index";
            $user_id = $jwt->verifyToken($token);
            if (!$user_id) {
                // token解析失败跳转登录页面
                return message("请登录", false, null, 401);
            }

            //判断token是否库的token一直
//            $member = Member::where(["token"=>$token])->find();
//            if(!$member){
//                return message("请登录", false, null, 401);
//            }
        } else {
            // 跳转至登录页面
            return message("请登录", false, null, 401);
        }
        return $next($request);
    }
}
