<?php

namespace app\model;

/**
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 * @property int $status
 *
 * @property int $lang 语言
 * @property string $m 模块
 * @property string $g 组别
 * @property string $k 名称
 * @property string $v 内容
 * @property int $is_load 自动加载
 *
 */
class Setting extends \app\model\AR 
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'setting';

    protected  $schema = [
        'id'           => 'int',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'status'       => 'int',
        'lang'         => 'tinyint', 
        'm'            => 'varchar', 
        'g'            => 'varchar', 
        'k'            => 'varchar', 
        'v'            => 'varchar', 
        'is_load'      => 'tinyint', 
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

