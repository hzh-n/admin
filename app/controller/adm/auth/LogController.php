<?php

namespace app\controller\adm\auth;

use think\App;
use app\model\AdminLog;

class LogController extends \app\controller\adm\_Controller
{
    public function listAction()
    {
        if ($this->request->isAjax()) {
            [$where, $page, $limit, $order] = $this->buildTableParames();

            $model = new AdminLog();
            $data  = $model->findList(['admin'], $where, $page, $limit, $order);

            return json($data);
        }

        return $this->view();
    }

    public function detailAction()
    {
        $row            = AdminLog::find($this->request->param(['ids']));
        $row['content'] = json_decode($row['content'], true);

        return $this->view([
            'row' => $row,
        ]);
    }

    public function delAction()
    {
        if ($this->request->isAjax()) {
            $row = AdminLog::whereIn('id', $this->admIds)
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
