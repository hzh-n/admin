<?php

declare(strict_types=1);

namespace app\middleware;

class CheckLogin
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        //验证后台是否登录
        if (!session('?admin') && $request->controller() != 'adm.Public') {
            return redirect('/adm/10soo_login');
        }

        return $next($request);
    }
}
