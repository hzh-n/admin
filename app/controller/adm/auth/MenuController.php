<?php

namespace app\controller\adm\auth;

use think\App;
use app\model\Menu;

class MenuController extends \app\controller\adm\_Controller
{
    public function listAction()
    {
        if ($this->request->isAjax()) {
            [$where, $page, $limit, $order] = $this->buildTableParames();
            $where[] = ['status', '>=', 0];
            $where[] = ['parent_id', '=', 0];

            $model = new Menu();
            $total = $model->filterWhere($where)
                ->count();

            $list = Menu::getTreeMenu();

            return json([
                'total' => $total,
                'rows'  => Menu::getMenu($list),
            ]);
        }

        return $this->view();
    }

    public function editAction()
    {
        $row = Menu::find($this->admIds);

        if ($this->request->isAjax()) {
            $param = $this->request->param('row');

            try {
                $save = $row->save($param);
            } catch (\Exception $e) {
                return $this->fail('保存失败:' . $e->getMessage());
            }

            return $save ? $this->success('保存成功') : $this->fail('保存失败');
        }

        $this->globals['row'] = $row;

        $this->globals['menu'] = Menu::getTreeMenu()
            ->toArray();

        return $this->view();
    }

    public function delAction()
    {
        if ($this->request->isAjax()) {
            $row = Menu::whereIn('id', $this->admIds)
                ->select();
            try {
                foreach ($row as $v) {
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