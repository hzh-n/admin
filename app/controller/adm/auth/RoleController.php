<?php

namespace app\controller\adm\auth;

use think\App;
use app\model\AdminRole;
use app\model\Cate;
use app\model\Menu;
use app\traits\commonAdm;

class RoleController extends \app\controller\adm\_Controller
{
    use commonAdm;

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = 'AdminRole';
        $this->order = 'id asc';
    }

    public function menuAction()
    {
        $param   = $this->request->param();
        $menuIds = !empty($param['id']) ? explode(',', AdminRole::find($param['id'])['menu_ids']) : [];

        $data = Menu::filterWhere(['status' => 1])
            ->field('id,parent_id as parent,title as text')
            ->order('sort desc,parent_id asc,id asc')
            ->select()
            ->toArray();

        foreach ($data as $k => $v) {
            if ($v['parent'] == 0) {
                $data[$k]['parent'] = '#';
            }

            if (in_array($v['id'], $menuIds) && $v['parent'] != 0) {
                $data[$k]['state']['selected'] = true;
            } else {
                $data[$k]['state']['selected'] = false;
            }

            $data[$k]['type'] = 'menu';
        }

        return $this->success($data);
    }

    public function cateAction()
    {
        $param   = $this->request->param();
        $cateIds = !empty($param['id']) ? explode(',', AdminRole::find($param['id'])['cate_ids']) : [];

        $data = Cate::filterWhere([
            ['status', '=', 1],
            ['is_index', '=', 0],
            ['id', 'not in', [1, 2]],
            ['parent_id', 'not in', [1, 2]],
        ])
            ->field('id,parent_id as parent,title as text')
            ->order('sort desc,parent_id asc,id asc')
            ->select()
            ->toArray();

        foreach ($data as $k => $v) {
            if ($v['parent'] == 0) {
                $data[$k]['parent'] = '#';
            }

            if (in_array($v['id'], $cateIds)) {
                $data[$k]['state']['selected'] = true;
            } else {
                $data[$k]['state']['selected'] = false;
            }

            $data[$k]['type'] = 'menu';
        }

        return $this->success($data);
    }
}
