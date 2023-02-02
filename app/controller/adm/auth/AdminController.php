<?php

namespace app\controller\adm\auth;

use shisou\tpinit\lib\Sha1;
use app\model\Admin;
use app\model\AdminLog;
use app\model\AdminRole;
use think\Model;

class AdminController extends \app\controller\adm\_Controller
{
    public function listAction()
    {
        if ($this->request->isAjax()) {
            $page  = $_GET['page'];
            $limit = $_GET['limit'] ?: 10;

            $where = [];
            if ($_GET['username']) {
                $where[] = ['username', 'LIKE', '%' . $_GET['username'] . '%'];
            }
            if ($_GET['mobile']) {
                $where[] = ['mobile', '=', $_GET['mobile']];
            }

            $data = (new Admin())->findList(null, $where, $page, $limit);

            return $this->admSuccess('', $data['total'], $data['rows']);
        }

        return $this->view();
    }


    public function addAction()
    {
        return $this->editAction();
    }

    public function editAction()
    {
        $row = Admin::findOrNew($this->admIds);

        if ($this->request->isAjax()) {
            $param = $this->request->param();

            $check = Admin::filterWhere(['username' => $param['username']])
                ->find();
            if ($check && $check->id != $this->admIds) {
                return $this->fail('用户名已存在');
            }
            if (!$this->admIds && empty($param['password'])) {
                return $this->fail('请输入密码');
            }
            if (!empty($param['password'])) {
                $param['pwd'] = Sha1::admLoginPwd($param['password']);
            }
            if ($param['status'] == 'on') {
                $param['status'] = 1;
            } else {
                $param['status'] = 0;
            }

            try {
                $save = $row->save($param);
            } catch (\Exception $e) {
                return $this->fail('保存失败:' . $e->getMessage());
            }

            return $save ? $this->success('保存成功') : $this->fail('保存失败');
        }

        $this->globals['row']  = $row;
        $this->globals['role'] = AdminRole::filterWhere(['status' => 1])
            ->select();

        return $this->view();
    }

    public function delAction()
    {
        if ($this->request->isAjax()) {
            if (strpos($this->admIds, '1') !== false) {
                return $this->fail('admin 不允许删除');
            }

            $row = Admin::whereIn('id', $this->admIds)
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

    public function logAction()
    {
        if ($this->request->isAjax()) {
            [$where, $page, $limit, $order] = $this->buildTableParames();

            $model = new AdminLog();

            $where['admin_id'] = $this->adm['id'];

            $data = $model->findList(['admin'], $where, $page, $limit, $order);

            return json($data);
        }

        return $this->view();
    }
}
