<?php

namespace app\controller;

use app\model\News;

class NewsController extends _Controller
{
    public function listAction()
    {
        $city = $this->request->get('city');

        $list = News::filterWhere([
            'status' => 1,
            'city'   => $city,
        ])
            ->order(['sort'=>'DESC', 'id' => 'DESC'])
            ->paginate(40);

        return $this->success('', $list);
    }

    public function viewAction()
    {
        $row = News::filterWhere(['id' => $this->reqId])
            ->with('gallery')
            ->find()
            ->toArray();

        if (empty($row['gallery'])) {
            $row['gallery'] = [
                [
                    'id'         => $row['id'],
                    'created_at' => $row['created_at'],
                    'updated_at' => $row['updated_at'],
                    'status'     => $row['status'],
                    'sort'       => $row['sort'],
                    'news_id'    => $row['id'],
                    'img_photo'  => $row['img_cover'],
                    'title'      => $row['title'],
                ],
            ];
        }

        return $this->success('', $row);
    }
}
