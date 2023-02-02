<?php

namespace app\controller;

use app\model\News;
use app\model\NewsGallery;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use shisou\tpinit\controller\_WebController;
use shisou\tpinit\lib\Qiniu;
use think\Model;

class TestController extends _WebController
{
    public function test1Action()
    {
        //反转义
        $str = "I' m &nbsp; a student";
//        $str = stripcslashes($str);
        echo $str;
    }

    public function testAction()
    {
        set_time_limit(0);

        $path     = public_path();
        $nameList = scandir($path . 'minsu');
        if (empty($nameList)) {
            return $this->fail('目录不存在');
        }
        // 获取文件夹所有名称
        $arr = [];
        foreach ($nameList as $v) {
            if ($v != '.' && $v != '..') {
                $arr[] = [
                    'name' => $v,
                ];
            }
        }

        // 七牛
        $qiNiu     = config('qiniu.qiniu');
        $uploadMgr = new UploadManager();

        // sort排序
        $num = NewsGallery::filterWhere(['status' => 1])
            ->field('sort')
            ->count();

        foreach ($arr as $item) {
            $new   = News::filterWhere(['status' => 1])
                ->where('name', $item['name'])
                ->find();
            $isNew = empty($new) ? [] : $new->toArray();
            if ($isNew) {
                $image = scandir($path . 'minsu' . '/' . $item['name']);
                foreach ($image as $v) {
                    $qiNiuPath = 'minsu' . '/' . date('YmdHis') . rand(10000, 99999);
                    $token     = (new Auth($qiNiu['accessKey'], $qiNiu['secretKey']))->uploadToken($qiNiu['bucket']);

                    if ($v != '.' && $v != '..' && $v != '1.jpg' && $v != 'Thumbs.db' && $v != '.DS_Store') {
                        $info = $uploadMgr->putFile($token, $qiNiuPath, $path . 'minsu' . '/' . $item['name'] . '/' . $v);
                        if ($info[0]['key']) {
                            (new NewsGallery())->save([
                                'title'     => $new['name'],
                                'news_id'   => $new['id'],
                                'img_photo' => $qiNiu['domain'] . '/' . $info[0]['key'],
                                'sort'      => $num++,
                            ]);
                        } else {
                            return $this->fail('');
                        }
                    }
                }
            }
        }

        return $arr ? $this->success('操作成功', $arr) : $this->fail('操作失败', $arr);
    }
}