<?php

namespace app\controller\adm;

use think\App;
use app\model\Cate;
use app\traits\commonAdm;

class CateController extends _Controller
{
    use commonAdm;

    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->globals['cate'] = Cate::getTreeCate()
            ->toArray();
    }

    public function listAction()
    {

        if ($this->request->isAjax()) {
            [$where, $page, $limit, $order] = $this->buildTableParames();
            $where[] = ['status', '>=', 0];
            $where[] = ['parent_id', '=', 0];

            $model = new Cate();
            $total = $model->filterWhere($where)
                ->order('sort desc,id asc')
                ->count();

            $list = Cate::getTreeCate()
                ->toArray();

            return json([
                'total' => $total,
                'rows'  => Cate::getCate($list),
            ]);
        }

        return $this->view(['layout' => false]);
    }

    public function editAction()
    {
        $row = Cate::findOrNew($this->admIds);

        if ($this->request->isAjax()) {
            $param         = $this->request->param('row');
            $param['type'] = 1;

            $cate = Cate::findOrNew($param['parent_id']);
            if ($cate) {
                $param['type'] = 2;
            }
            if ($cate->parent) {
                $param['type'] = 3;
            }
            if ($cate->parent->parent) {
                $param['type'] = 4;
            }

            try {
                $save = $row->save($param);
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
            $row = Cate::whereIn('id', $this->admIds)
                ->select();
            try {
                foreach ($row as $v) {
                    if (!$v->children->isEmpty()) {
                        foreach ($v->children as $v1) {
                            if (!$v1->children->isEmpty()) {
                                foreach ($v1->children as $v2) {
                                    $save = $v2->save(['status' => -1]);
                                }
                            }
                            $save = $v1->save(['status' => -1]);
                        }
                    }
                    $save = $v->save(['status' => -1]);
                }
            } catch (\Exception $e) {
                return $this->fail('删除失败');
            }

            return $save ? $this->success('删除成功') : $this->fail('删除失败');
        }

        return $this->fail('非法操作');
    }
}
