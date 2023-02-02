<?php

namespace app\controller\adm;

use app\model\Admin;
use think\App;

class _Controller extends \shisou\tpinit\controller\_Controller
{
    protected $adm;
    protected $admIds = 0;

    protected $middleware = [
        \app\middleware\CheckLogin::class,
        \app\middleware\AdmAuth::class,
        \app\middleware\AdmLog::class,
    ];

    public function __construct(App $app)
    {
        parent::__construct($app);

        if (session('?admin')) {
            $this->adm              = Admin::find(session('admin')['id']);
            $this->globals['admin'] = $this->adm;
        }

        $this->admIds = $this->request->param('ids') ?? 0;

    }

    /**
     * 构建请求参数
     * @param array $excludeFields 忽略构建搜索的字段
     * @return array
     */
    protected function buildTableParames($excludeFields = [])
    {

        $param = $this->request->param();

        $offset  = isset($param['offset']) && !empty($param['offset']) ? $param['offset'] : 0;
        $limit   = isset($param['limit']) && !empty($param['limit']) ? $param['limit'] : 15;
        $filters = isset($param['filter']) && !empty($param['filter']) ? $param['filter'] : '{}';
        $ops     = isset($param['op']) && !empty($param['op']) ? $param['op'] : '{}';

        //计算页码
        $page = $limit ? intval($offset / $limit) + 1 : 1;

        // json转数组
        $filters = json_decode($filters, true);
        $ops     = json_decode($ops, true);
        $where   = $excludes = [];

        $order = 'id desc';
        if (!empty($param['sort'])) {
            $order = $param['sort'] . ' ' . $param['order'] . ',id desc';
        }

        foreach ($filters as $key => $val) {
            if (in_array($key, $excludeFields)) {
                $excludes[$key] = $val;
                continue;
            }

            $op = isset($ops[$key]) && !empty($ops[$key]) ? $ops[$key] : 'LIKE';

            switch (strtolower($op)) {
                case '=':
                    $where[] = [$key, '=', str_replace('id-', '', $val)];
                    break;
                case 'like':
                    $where[] = [$key, 'LIKE', "%{$val}%"];
                    break;
                case 'between':
                    if ($key == 'created_at' || $key == 'updated_at') {
                        [$beginTime, $endTime] = explode(',', $val);
                        $val = date('Y-m-d H:i:s', $beginTime) . ',' . date('Y-m-d H:i:s', $endTime);
                    }
                    $where[] = [$key, 'BETWEEN', $val];
                    break;
                default:
                    $where[] = [$key, 'LIKE', "%{$val}%"];
            }

        }

        return [$where, $page, $limit, $order, $excludes];
    }

    public function view($view = null, $param = null)
    {
        if (is_array($view) || !$view) {
            $param = $view;
            $view  = $this->request->action();
        }

        $arr = [
            'hideNav' => false,
            'user'    => session('user'),
        ];
        if ($param) {
            $arr = array_merge($arr, $param);
        }
        $param = array_merge($arr, $this->globals);

//        if (!isset($param['layout'])) {
//            $this->app->view->layout(app()->getRootPath() . 'view/adm/_head.html');
//        }

        return view($view, $param);
    }

    public function admSuccess($msg = '', $count = null, $data = null)
    {
        if (is_array($msg)) {
            $data = $msg;
            $msg  = '';
        }

        return $this->response(1, $msg, $count, $data);
    }

    protected function response($code, $msg, $count, $data)
    {
        $json = [
            'code'  => $code,
            'count' => $count,
            'msg'   => $msg,
            'data'  => $data,
        ];

        return json($json);
    }
}
