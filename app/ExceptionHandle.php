<?php

namespace app;

use phpDocumentor\Reflection\Types\String_;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use shisou\tpinit\lib\ErrorNotice;
use think\facade\Log;
use think\Response;
use Throwable;

/**
 * 应用异常处理类
 * @version 20220728
 */
class ExceptionHandle extends Handle
{
    /**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];

    /**
     * 记录异常信息（包括日志或者其它方式记录）
     *
     * @access public
     * @param  Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        // 使用内置的方式记录异常日志
        parent::report($exception);

        // 自定义记录异常日志（也可入库）
        // if ($exception instanceof HttpException) {
        //     // 404
        // }

        // 500
        if (!empty($exception->getMessage())) {
            $err = PHP_EOL . 'msg ：' . $this->getMessage($exception) . PHP_EOL . 'file：' . $exception->getFile() . PHP_EOL . 'line：' . $exception->getLine() . PHP_EOL . 'url：' . request()->url() . PHP_EOL . 'param：' . json_encode(request()->param());

            Log::record($err);

            (new ErrorNotice($err))->send();
        }
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param \think\Request   $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        // 添加自定义异常处理机制
        // if ($e instanceof HttpException) {
        //     // return $this->json($e->getStatusCode(), $e->getMessage());
        //     return view(root_path() . '/public/static/error/404.html', ['e' => $e], 404);
        // }

//         if (!empty($e->getMessage())) {
//             // return $this->json($e->getCode(), $e->getMessage());
//             return view(root_path() . '/public/static/error/500.html', ['e' => $e], 500);
//         }

        // 其他错误交给系统处理
        return parent::render($request, $e);
    }

    /**
     * 返回json数据
     *
     * @param integer $code 状态码
     * @param string $msg   描述信息
     */
    private function json(int $code, string $msg)
    {
        return json(compact('code', 'msg'));
    }
}
