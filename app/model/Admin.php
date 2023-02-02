<?php

namespace app\model;

/**
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 * @property int $status
 *
 * @property int $role_id @admin_role
 * @property string $username 用户名
 * @property string $pwd 密码
 * @property string $mobile 手机
 *
 * @property AdminRole $role
 * @property News[] $newsList
 */
class Admin extends \app\model\AR 
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'admin';

    protected  $schema = [
        'id'           => 'int',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'status'       => 'int',
        'role_id'      => 'int', 
        'username'     => 'varchar', 
        'pwd'          => 'varchar', 
        'mobile'       => 'varchar', 
    ];


    public static function findOne($data = null): self
    {
        if (is_numeric($data)) {
            $data = ['id' => $data];
        }

        return self::where($data)
            ->find();
    }

    public function role()
    {
        return $this->hasOne(AdminRole::class, 'id' , 'role_id');
    }

    public function newsList()
    {
        return $this->hasMany(News::class, 'admin_id' , 'id');
    }

    // ---------- Custom code below ----------

    public function getAuthMenuIds()
    {
        $menuIds = $this->role ? $this->role['menu_ids'] : '';

        return explode(',', $menuIds);
    }

    public function getAuthCateIds()
    {
        $menuIds = $this->role ? $this->role['cate_ids'] : '';

        return explode(',', $menuIds);
    }

    public function getAuthMenuUrls()
    {
        $menuUrls = Menu::filterWhere([['id', 'in', $this->getAuthMenuIds()]])
            ->field('url')
            ->select()
            ->toArray();

        $urls = array_values(array_filter(array_unique(array_column($menuUrls, 'url'))));

        return array_merge($urls, config('params')['public_auth']);
    }
}

