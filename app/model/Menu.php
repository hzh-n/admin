<?php

namespace app\model;

/**
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 * @property int $status
 *
 * @property int $parent_id @menu@
 * @property int $sort 排序
 * @property int $type 菜单类型
 * @property string $title 标题
 * @property string $url URL
 * @property string $icon 图标
 * @property string $memo 备注
 *
 * @property Menu $parent
 * @property Menu[] $children
 */
class Menu extends \app\model\AR 
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'menu';

    protected  $schema = [
        'id'           => 'int',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'status'       => 'int',
        'parent_id'    => 'int', 
        'sort'         => 'int', 
        'type'         => 'tinyint', 
        'title'        => 'varchar', 
        'url'          => 'varchar', 
        'icon'         => 'varchar', 
        'memo'         => 'varchar', 
    ];


    public static function findOne($data = null): self
    {
        if (is_numeric($data)) {
            $data = ['id' => $data];
        }

        return self::where($data)
            ->find();
    }

    public function parent()
    {
        return $this->hasOne(Menu::class, 'id' , 'parent_id');
    }

    public function Children()
    {
        return $this->hasMany(Menu::class, 'parent_id' , 'id');
    }

    // ---------- Custom code below ----------

    public static function getTreeMenu()
    {
        return self::filterWhere([['status', '>=', 0], ['parent_id', '=', 0]])
            ->with([
                'children' => function ($query) {
                    $query->where([['status', '>=', 0]])->order('sort desc,id asc');
                }, 'children.children' => function ($query) {
                    $query->where([['status', '>=', 0]])->order('sort desc,id asc');
                },
                'children.children.children' => function ($query) {
                    $query->where([['status', '>=', 0]])->order('sort desc,id asc');
                }
            ])->field('*')
            ->order('sort desc,id asc')
            ->select();
    }

    public static function getMenu($menu, &$result = [])
    {
        foreach ($menu as  $val) {
            $result[] = $val;
            if (!empty($val['children'])) {
                self::getMenu($val['children'], $result);
            }
        }
        return $result;
    }

    public function getLogTitle()
    {
        return $this->parent->parent->title . '/' . $this->parent->title;
    }
}

