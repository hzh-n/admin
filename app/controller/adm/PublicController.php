<?php

namespace app\controller\adm;

use think\Request;
use shisou\tpinit\lib\Sha1;
use app\model\Cate;
use app\model\Admin;
use app\model\News;
use think\facade\Filesystem;
use shisou\tpinit\lib\Qiniu;
use Qiniu\Storage\UploadManager;

class PublicController extends _Controller
{
    public function loginAction(Request $request)
    {
        if ($this->request->isPost()) {
            $param = $request->param();
            if (!$param['username'] || !$param['password']) {
                return $this->fail('参数不能为空');
            }

//            if (!captcha_check($param['captcha'])) {
//                return $this->fail('验证码错误');
//            }

            $where['username'] = $param['username'];
            $where['pwd']      = Sha1::admLoginPwd($param['password']);

            $admin = Admin::filterWhere($where)
                ->find();

            if (!$admin) {
                return $this->fail('账号或密码错误');
            }

            if ($admin['status'] != 1) {
                return $this->fail('账号禁用');
            }
            session('admin', $admin);

            return $this->success();
        }

        return $this->view(['layout' => false]);
    }

    public function uploadsAction()
    {
        $qiniu = new Qiniu;
        $file  = request()->file('file');
        if (!$file) {
            return $this->fail('没有选择上传文件');
        }

        try {
            //验证文件规则
            $result = validate(['file' => ['fileSize:10240000,fileExt:gif,jpg,png']])->check(['file' => $file]);

            if ($result) {
                $img = $qiniu->upload($file);
                $url = ['url' => $img, 'fullurl' => $img];
            }

            return $this->success('成功', $url);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage());
        }
    }

    public function uploadAction()
    {
        $file = request()->file('file');
        if (!$file) {
            return $this->fail('没有上传文件');
        }

        try {
            //验证文件规则
            $result = validate(['file' => ['fileSize:20480000,fileExt:gif,jpg,png,mp4,xls,xlsx,doc,docx']])
                ->check(['file' => $file]);

            $saveName = [];
            if ($result) {
                $saveName[] = Filesystem::disk('public')
                    ->putFile('uploads', $file, 'uniqid');

                foreach ($saveName as $v) {
                    $url = '/storage/' . $v;
                }

                $data = ['url' => $url, 'fullurl' => $url];

                return $this->success('成功', $data);
            }
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage());
        }
    }

    public function icoAction()
    {
        return file_get_contents(public_path() . '/static/adm/assets/libs/font-awesome/less/variables.less');
    }

    public function cateAction()
    {
        $cateId = $this->request->param('cate_id') ?: 0;

        return Cate::getAdmSearchCate($cateId);
    }

    public function searchNewsAction()
    {
        $news = News::filterWhere([
            ['status', '=', 1],
        ])
            ->field('id,name')
            ->order('sort desc,id asc')
            ->select();

        $data = [];
        foreach ($news as $v) {
            $data['id-' . $v['id']] = $v['name'];
        }

        return $data;
    }

    public function adminAction()
    {
        $list = Admin::where(['status' => 1])
            ->field('id,username')
            ->select();

        foreach ($list as $v) {
            $result[$v['id']] = $v['username'];
        }

        return $result;
    }

    public function specialAction()
    {
        $param = $this->request->param();
        $model = new Cate();

        $where[] = ['parent_id', '=', 32];
        if (isset($param['pkey_value'])) {
            $data['total'] = count(explode(',', $param['pkey_value']));
            $where[]       = [$param['pkey_name'], 'in', $param['pkey_value']];
            $data['rows']  = $model->filterWhere($where)
                ->order('sort desc,id desc')
                ->select();
        } else {
            $where[] = ['status', '=', 1];
            if (!empty($param['title'])) {
                $where[] = ['title', 'like', '%' . $param['title'] . '%'];
            }

            $data = $model->findList(null, $where, $param['page'], $param['per_page'], 'id desc', 'id,title');
        }

        return json($data);
    }

    public function newsAction()
    {
        $param = $this->request->param();
        $model = new News();

        $where[] = ['cate_id', 'in', Cate::getSubIds(4)];
        if (isset($param['pkey_value'])) {
            $data['total'] = count(explode(',', $param['pkey_value']));
            $where[]       = [$param['pkey_name'], 'in', $param['pkey_value']];
            $data['rows']  = $model->filterWhere($where)
                ->order('sort desc,id desc')
                ->select();
        } else {
            $where[] = ['status', '=', 1];
            if (!empty($param['title'])) {
                $where[] = ['title', 'like', '%' . $param['title'] . '%'];
            }

            $data = $model->findList(null, $where, $param['page'], $param['per_page'], 'id desc', 'id,title');
        }

        return json($data);
    }
}
