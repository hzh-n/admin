<?php

namespace app\controller\adm\system;

use app\model\Cate;
use app\model\Setting;

class SettingController extends \app\controller\adm\_Controller
{
    public function listAction()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param('row');

            foreach ($param as $key => $val) {
                $model = Setting::filterWhere(['k' => $key])
                    ->find();
                if (is_array($val)) {
                    $val = implode(',', $val);
                }

                if ($key == 'skin') {
                    $data['is_load'] = $val;
                } else {
                    $data['v'] = $val;
                }

                $data['updated_at'] = DATETIME;
                if (!$model->save($data)) {
                    return $this->fail('修改失败');
                }
            }

            return $this->success('修改成功');
        }

        $this->globals['row']  = Setting::select();
        $this->globals['cate'] = Cate::select();

        return $this->view();
    }
}
