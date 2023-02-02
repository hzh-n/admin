<?php

namespace app\controller\adm;

use app\model\Cate;
use app\model\News;
use app\model\NewsGallery;

class GalleryController extends _Controller
{
    public function listAction()
    {
        if ($this->request->isAjax()) {
            [$where, $page, $limit, $order] = $this->buildTableParames();

            $model = new NewsGallery();
            $data  = $model->findList(['news'], $where, $page, $limit, $order);

            return json($data);
        }

        return $this->view();
    }

    public function editAction()
    {
        $row = NewsGallery::findOrNew($this->admIds);

        if ($this->request->isAjax()) {
            $param = $this->request->param('row');

            try {
                $save = $row->save($param);
            } catch (\Exception $e) {
                return $this->fail('保存失败:' . $e->getMessage());
            }

            return $save ? $this->success('保存成功') : $this->fail('保存失败');
        }

        $this->globals['row']  = $row;
        $this->globals['news'] = News::filterWhere(['status' => 1])
            ->field(['id', 'name'])
            ->select()
            ->toArray();

        return $this->view();
    }

    public function delAction()
    {

        if ($this->request->isAjax()) {
            $row = NewsGallery::whereIn('id', $this->admIds)
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