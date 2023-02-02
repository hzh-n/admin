<?php

namespace app\traits;

use think\Request;

trait commonWeb
{
    /**
     * @return AR
     */
    private function getModelClass($model)
    {
        /* @var AR $modelClass */
        $curModel = $model ?: $this->request->controller();

        $modelClass = "\\app\\model\\" . $curModel;

        return $modelClass;
    }

    public function commonPage($where = [], $curModel = null, $limit = 12, $order = null)
    {
        $model = $this->getModelClass($curModel);

        $where[] = ['status', '=', 1];

        $order = is_null($order) ? 'sort desc,id desc' : $order;

        $list = (new $model)->filterWhere($where)
            ->order($order)
            ->paginate([
                'list_rows' => $limit,
                'var_page' => 'p',
            ]);

        $this->globals['list']  = $list;

        return [
            'list'   => $list,
        ];
    }

    public function commonAjax($where = [], $page = null, $curModel = null,  $limit = 12, $order = null,  $fields = null)
    {
        $model = $this->getModelClass($curModel);

        $where = is_null($where) ? ['status' => 1] : $where;
        $order = is_null($order) ? 'sort desc,id desc' : $order;

        $list = (new $model)->findList(null, $where, $page, $limit, $order, $fields);

        $this->globals['total'] = $list['total'];
        $this->globals['row']   = $list['rows'];

        return [
            'total' => $list['total'],
            'row'   => $list['rows'],
        ];
    }
}
