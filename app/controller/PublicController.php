<?php

namespace app\controller;

use app\model\Cate;
use app\model\News;
use shisou\tpinit\controller\_CmsController;
use think\Model;

class PublicController extends _Controller
{
    public function detailsAction()
    {
        $param = $this->request->param();

        $where['status'] = 1;

        if ($param['row'] && session('?admin')) {
            $row = $param['row'];

            $row['id']   = 0;
            $where['id'] = $row['id'];
        } else {
            $where['id'] = $param['id'];
            $row         = News::filterWhere($where)
                ->find();
            $row->cnt_view++;
            $row->save();

            unset(
                $where['id']
            );
        }

        if (\shisou\tpinit\isMobile()) {
            $cateWhere[] = ['cate_id', 'in', Cate::getSubIds($row->cate->parent->id)];
        } else {
            $cateWhere[] = ['cate_id', '=', $row['cate_id']];
        }

        $res = News::filterWhere($where)
            ->where($cateWhere)
            ->where([['id', '<>', $row['id']], ['sort', '=', $row['sort']]])
            ->find();

        $sort = false;
        if ($res) {
            $preWhere[]  = ['id', '>', $row['id']];
            $nextWhere[] = ['id', '<', $row['id']];
        } else {
            $sort        = true;
            $preWhere[]  = ['sort', '>', $row['sort']];
            $nextWhere[] = ['sort', '<', $row['sort']];
        }

        $pre = News::filterWhere($where)
            ->where($cateWhere)
            ->where($preWhere)
            ->order('sort asc,id asc')
            ->find();
        if (!$pre && $sort) {
            $pre = News::filterWhere($where)
                ->where($cateWhere)
                ->where([['id', '>', $row['id']]])
                ->order('sort asc,id asc')
                ->find();
        }

        $next = News::filterWhere($where)
            ->where($cateWhere)
            ->where($nextWhere)
            ->order('sort desc,id desc')
            ->find();
        if (!$next && $sort) {
            $next = News::filterWhere($where)
                ->where($cateWhere)
                ->where([['id', '<', $row['id']]])
                ->order('sort desc,id desc')
                ->find();
        }

        $rec = News::filterWhere($where)
            ->order('is_rec desc,id desc')
            ->limit(15)
            ->select();

        return $this->view('details', [
            'row'  => $row,
            'pre'  => $pre,
            'next' => $next,
            'rec'  => $rec,
        ]);
    }

    public function searchAction()
    {
        return $this->view('search', []);
    }
}