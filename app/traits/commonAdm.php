<?php

namespace app\traits;

use think\Request;

trait commonAdm
{
    private $model;
    /**
     * @return AR
     */
    private function getModelClass()
    {
        /* @var AR $modelClass */
        $curModel = $this->model ?: $this->request->controller();

        $modelClass = "\\app\\model\\" . str_replace('adm.', '', $curModel);

        return $modelClass;
    }

    public function listAction()
    {
        $model = $this->getModelClass();

        if ($this->request->isAjax()) {
            list($where, $page, $limit, $order) = $this->buildTableParames();

            $with   = $this->with ?? null;

            $fields = $this->fields ?? null;

            if (!empty($this->where)) {
                $where = array_merge($where, $this->where);
            }

            if (!empty($this->order)) {
                $order = $this->order . ',' . $order;
            }

            if (!empty($this->limit)) {
                $limit = $this->$limit;
            }

            $data = (new $model)->findList($with, $where, $page, $limit, $order, $fields);

            return json($data);
        }

        return $this->view();
    }

    public function editAction()
    {
        $model = $this->getModelClass();

        $row = $model::findOrNew($this->admIds);

        if ($this->request->isAjax()) {
            $param = $this->request->param('row');

            $param['is_show'] = $param['is_show'] ?? 0;
            if (empty($param['cate_id'])) {
                $param['cate_id'] = $this->cate_id ?? 0;
            }
            if (empty($param['add_time'])) {
                $param['add_time'] = date('Y-m-d', time());
            }

            try {
                $save = $row->save($param);
            } catch (\Exception $e) {
                return $this->fail('保存失败:' . $e->getMessage());
            }
            return $save ? $this->success('保存成功') : $this->fail('保存失败');
        }

        $this->globals['row'] = $row;

        return $this->view();
    }

    public function delAction()
    {
        $model = $this->getModelClass();

        if ($this->request->isAjax()) {
            $row = $model::whereIn('id', $this->admIds)->select();

            try {
                $save = $row->delete();
            } catch (\Exception $e) {
                return $this->fail('删除失败');
            }

            return $save ? $this->success('删除成功') : $this->fail('删除失败');
        }

        return $this->fail('非法操作');
    }
}
