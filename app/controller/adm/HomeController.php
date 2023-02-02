<?php

namespace app\controller\adm;

use shisou\tpinit\lib\Sha1;
use app\model\Admin;
use app\model\Cate;
use app\model\Menu;
use app\model\News;
use app\Request;

class HomeController extends _Controller
{
    public function indexAction(Request $request)
    {
        $this->globals['menus'] = Menu::getTreeMenu();

        $this->globals['authIds'] = $this->adm->getAuthMenuIds();

        return $this->view();
    }

    public function profileAction()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param('row');
            $admin = Admin::find($this->globals['admin']['id']);

            if (!empty($param['password'])) {
                $admin->pwd = Sha1::admLoginPwd($param['password']);
            }
            $admin->mobile     = $param['mobile'];
            $admin->updated_at = DATETIME;

            if (!$admin->save()) {
                return $this->fail('修改失败');
            }

            return $this->success('修改成功');
        }

        return $this->view([
            'row' => $this->globals['admin'],
        ]);
    }

    public function mainAction()
    {
        $mainCnt['admin'] = Admin::where([])
            ->count();
        $mainCnt['news']  = News::where([['status', '=', 1], ['cate_id', 'not in', Cate::getSubIds(7)]])
            ->count();

        return $this->view([
            'mainCnt' => $mainCnt
        ]);
    }

    public function logoutAction()
    {
        session('admin', null);

        return redirect('/adm/10soo_login');
    }
}