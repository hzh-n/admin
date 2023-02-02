<?php

declare(strict_types=1);

namespace app\middleware;

class AdmAuth
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
        if ($request->controller() != 'adm.Public') {
            //查看权限
            $admin = \app\model\Admin::find(session('admin')['id']);

            if ($admin['id'] != 1 && !in_array(explode('?', $request->url())[0], $admin->getAuthMenuUrls())) {
                $msg = ['code' => 0, 'msg' => '没有权限'];
                return json($msg);
            }
        }

        return $next($request);
    }
}
