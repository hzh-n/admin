<?php

namespace app\controller\adm;

use app\model\News;
use app\model\NewsGallery;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use shisou\tpinit\lib\ImportExcel;

class ImportController extends _Controller
{
    public function excelAction()
    {

        $file = request()->file('file');

        $num = News::filterWhere(['status' => 1])
            ->max('sort');

        if ($file) {
            $saveName = \think\facade\Filesystem::disk('public')
                ->putFile('uploads', $file, 'uniqid');
            $excel    = '/storage/' . $saveName;

            $url = ['url' => $excel, 'fullurl' => $excel];

            $dataKey = ['A' => 'name', 'B' => 'city', 'C' => 'county', 'D' => 'address', 'E' => 'phone', 'F' => 'content'];

            $list = (new ImportExcel($url['url'], $dataKey))->getData();

            $data = [];
            foreach ($list as $v) {
                $news  = News::filterWhere(['status' => 1])
                    ->where('name', $v['name'])
                    ->find();
                $isNew = empty($news) ? [] : $news->toArray();
                if (!$isNew) {
                    $data[] = [
                        'add_time'   => DATETIME,
                        'created_at' => DATETIME,
                        'updated_at' => '0000-00-00 00:00:00',
                        'is_rec'     => 1,
                        'cate_id'    => 2,
                        'name'       => $v['name'],
                        'city'       => $v['city'],
                        'county'     => $v['county'],
                        'address'    => $v['address'],
                        'phone'      => $v['phone'],
                        'content'    => nl2br($v['content']),
                        'sort'       => intval($num++) + 1,
                    ];
                }
            }

            $save = (new News())->saveAll($data);

            return $save ? $this->success('操作成功') : $this->fail('操作失败');
        }

        return $this->success('操作成功');
    }

    public function newsAction()
    {
        set_time_limit(0);

        $path     = public_path() . 'minsu';
        $nameList = scandir($path);
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

        foreach ($arr as $item) {
            $image = scandir($path . '/' . $item['name']);
            foreach ($image as $v) {

                /********* 存入七牛云 *********/
                $qiNiuPath = 'minsu' . '/' . date('YmdHis') . rand(1000, 9999);
                $token     = (new Auth($qiNiu['accessKey'], $qiNiu['secretKey']))->uploadToken($qiNiu['bucket']);

                /********* news表存入img_cover *********/
                if ($v != '.' && $v != '..' && $v == '1.jpg') {
                    $info = $uploadMgr->putFile($token, $qiNiuPath, $path . '/' . $item['name'] . '/' . $v);
                    if ($info[0]['key']) {
                        (new News())::where(['name' => $item['name']])
                            ->update([
                                'img_cover' => $qiNiu['domain'] . '/' . $info[0]['key'],
                            ]);
                    }
                }
            }
        }

        return $arr ? $this->success('操作成功', $arr) : $this->fail('操作失败', $arr);
    }

    public function galleryAction()
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
            ->max('sort');

        foreach ($arr as $item) {
            $new = News::filterWhere(['status' => 1])
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
                                'sort'      => intval($num++) + 1,
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
