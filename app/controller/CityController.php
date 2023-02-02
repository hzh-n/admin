<?php

namespace app\controller;

use app\model\News;

class CityController extends _Controller
{
    public function listAction()
    {
        $params = config('params');
        $cities = $params['cities'];

        $cityList = [];
        foreach ($cities as $v) {
            $cityList[$v] = [
                'name'     => $v,
                'cnt'      => News::where(['city' => $v])
                    ->count(),
                'newsList' => News::where(['city' => $v])
                    ->limit(5)
                    ->order('sort DESC')
                    ->select(),
            ];
        }

        return $this->success($cityList);
    }
}