<?php

namespace app\model;

use shisou\tpinit\lib\Sha1;

/**
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 * @property int $status
 *
 * @property int $admin_id @admin@
 * @property string $title 标题
 * @property string $url 链接
 * @property string $content 内容
 * @property string $ip IP
 * @property string $useragent 设备
 *
 * @property Admin $admin
 */
class AdminLog extends \app\model\AR 
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'admin_log';

    protected  $schema = [
        'id'           => 'int',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'status'       => 'int',
        'admin_id'     => 'int', 
        'title'        => 'varchar', 
        'url'          => 'varchar', 
        'content'      => 'text', 
        'ip'           => 'varchar', 
        'useragent'    => 'varchar', 
    ];


    public static function findOne($data = null): self
    {
        if (is_numeric($data)) {
            $data = ['id' => $data];
        }

        return self::where($data)
            ->find();
    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'id' , 'admin_id');
    }

    // ---------- Custom code below ----------

    /**
     * 记录日志
     */
    public static function record($request)
    {
        $param = $request->param();
        $url = $request->url();

        switch ($request->action()) {
            case 'login':
                $title = '登陆';
                $content = '';

                $where['username'] = $param['username'];
                $where['pwd']      = Sha1::admLoginPwd($param['password']);
                $admin = Admin::filterWhere($where)->find();
                break;

            default:
                $admin = session('admin');
                $title = Menu::filterWhere(['url' => explode('?', $url)[0]])->find()->getLogTitle();

                if ($request->action() == 'del') {
                    $title  .= '/删除';
                    $content = ['ids' => $param['ids']];
                }
                if ($request->action() == 'edit' && empty($param['ids'])) {
                    $title  .= '/添加';
                    $content = ['content' => $param['row']];
                }
                if ($request->action() == 'edit' && !empty($param['ids'])) {
                    $title  .= '/修改';
                    $content = ['ids' => $param['ids']];
                }

                if ($request->action() == 'repeat') {
                    $title  .= '/还原';
                    $content = ['ids' => $param['ids']];
                }

                break;
        }

        if ($admin) {
            self::create([
                'admin_id'  => $admin['id'],
                'title'     => $title,
                'url'       => $url,
                'content'   => json_encode($content),
                'ip'        => $request->ip(),
                'useragent' => $request->header()['user-agent'],
            ]);
        }
    }
}

