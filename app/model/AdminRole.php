<?php

namespace app\model;

/**
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 * @property int $status
 *
 * @property string $name 角色
 * @property string $mome 备注
 * @property string $menu_ids 权限
 * @property string $cate_ids 栏目内容权限
 *
 */
class AdminRole extends \app\model\AR 
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'admin_role';

    protected  $schema = [
        'id'           => 'int',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'status'       => 'int',
        'name'         => 'varchar', 
        'mome'         => 'varchar', 
        'menu_ids'     => 'varchar', 
        'cate_ids'     => 'varchar', 
    ];


    public static function findOne($data = null): self
    {
        if (is_numeric($data)) {
            $data = ['id' => $data];
        }

        return self::where($data)
            ->find();
    }

    // ---------- Custom code below ----------
}

