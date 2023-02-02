<?php

namespace app\controller\adm;

use think\App;
use app\model\Cate;
use app\model\News;

class NewsController extends \app\controller\adm\_Controller
{
    public function listAction()
    {
        if ($this->request->isAjax()) {
            [$where, $page, $limit, $order] = $this->buildTableParames();

            $with = ['cate', 'admin'];

           // $order = 'is_rec desc,sort desc,id desc';

            if (!empty($this->limit)) {
                $limit = $this->$limit;
            }

            if (!empty($this->order)) {
                $order = $this->order . ',' . $order;
            }

            $data = (new News)->findList($with, $where, $page, $limit, $order);

            return json($data);
        }

        return $this->view();
    }

    public function editAction()
    {
        $row = News::findOrNew($this->admIds);

        if ($this->request->isAjax()) {

            $param = $this->request->param('row');

//            if (empty($param['cate_id'])) {
//                return $this->fail('栏目不能为空');
//            }

            if (empty($param['add_time'])) {
                $param['add_time'] = date('Y-m-d H:i', time());
            }

            try {
                $save = $row->save($param);

                //(new Tags)->add($param['tags'], $param['cate_id']);
            } catch (\Exception $e) {
                return $this->fail('保存失败:' . $e->getMessage());
            }

            return $save ? $this->success('保存成功') : $this->fail('保存失败');
        }

        $this->globals['row']  = $row;
        $this->globals['cate'] = Cate::getTreeCate()
            ->toArray();

        return $this->view();
    }

    public function delAction()
    {

        if ($this->request->isAjax()) {
            $row = News::whereIn('id', $this->admIds)
                ->select();

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
