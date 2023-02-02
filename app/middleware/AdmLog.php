<?php

declare(strict_types=1);

namespace app\middleware;

use app\model\Cate;

class AdmLog
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
        //操作日志
        if (
            ($request->isPost() || $request->isAjax()) &&
            in_array($request->action(), ['login', 'edit', 'del', 'repeat'])
        ) {
            \app\model\AdminLog::record($request);
        }

        return $next($request);
    }
}
