<?php

namespace app\model;

/**
 * @property int    $id
 * @property string $created_at
 * @property string $updated_at
 * @property int    $status
 *
 * @property int    $parent_id    @cate@
 * @property int    $is_nav       是否导航
 * @property int    $is_index     是否首页
 * @property int    $sort         排序
 * @property int    $type         类型
 * @property string $title        标题
 * @property string $sub_title    副标题
 * @property string $img_cover    封面图
 * @property string $img_bg       背景图
 * @property string $alias        别名
 * @property string $point        锚点
 * @property string $description  摘要
 * @property string $page_content 单页面内容
 * @property string $link_url     链接地址
 * @property string $tpl_list     列表页模板
 * @property string $tpl_show     详情页模板
 * @property string $tpl_page     单页面模板
 *
 * @property Cate   $parent
 * @property Cate[] $children
 * @property News[] $newsList
 */
class Cate extends \app\model\AR
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'cate';

    protected $schema = [
        'id'           => 'int',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'status'       => 'int',
        'parent_id'    => 'int',
        'is_nav'       => 'tinyint',
        'is_index'     => 'tinyint',
        'sort'         => 'int',
        'type'         => 'tinyint',
        'title'        => 'varchar',
        'sub_title'    => 'varchar',
        'img_cover'    => 'varchar',
        'img_bg'       => 'varchar',
        'alias'        => 'varchar',
        'point'        => 'varchar',
        'description'  => 'varchar',
        'page_content' => 'text',
        'link_url'     => 'varchar',
        'tpl_list'     => 'varchar',
        'tpl_show'     => 'varchar',
        'tpl_page'     => 'varchar',
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
        return $this->hasOne(Cate::class, 'id', 'parent_id');
    }

    public function Children()
    {
        return $this->hasMany(Cate::class, 'parent_id', 'id');
    }

    public function newsList()
    {
        return $this->hasMany(News::class, 'cate_id', 'id');
    }

    // ---------- Custom code below ----------

    public function tagsList()
    {
        return $this->hasMany(Tags::class, 'cate_id', 'id');
    }

    public function url($param = [])
    {
        if ($this->alias == '/') {
            return $this->alias;
        }
        if ($this->alias == '') {
            return 'javascript:;';
        }

        return url(session('lang') . '/' . $this->alias, $param) . $this->point ?: '';
    }

    public function recUrl($param = [])
    {
        if ($this->alias == '/') {
            return $this->alias;
        }

        if ($this->alias != '') {
            return url(session('lang') . '/' . $this->alias, $param) . $this->point ?: '';
        }

        foreach ($this->Children as $v) {
            if ($v->alias != '') {
                return url(session('lang') . '/' . $v->alias, $param) . $v->point ?: '';
                break;
            }
        }
    }

    public function isCur($cateId = 0)
    {
        $cateId = $cateId ?: __CURCATEID__;

        $subIds = self::getSubIds($this->id);

        if (in_array($cateId, $subIds)) {
            return true;
        }

        return false;
    }

    public function cate($id)
    {
        return self::find($id);
    }

    public static function getTreeCate($parentId = 0)
    {
        return self::filterWhere([['status', '>=', 0], ['parent_id', '=', $parentId]])
            ->with(['children'     => function ($query) {
                $query->where([['status', '>=', 0]])
                    ->order('sort desc,id asc');
            }, 'children.children' => function ($query) {
                $query->where([['status', '>=', 0]])
                    ->order('sort desc,id asc');
            }])
            ->field('*')
            ->order('sort desc,id asc')
            ->select();
    }

    public static function getCate($cate, &$result = [])
    {
        foreach ($cate as $val) {
            if ($val['status'] == -1) {
                continue;
            }

            $result[] = $val;
            if (!empty($val['children'])) {
                self::getCate($val['children'], $result);
            }
        }

        return $result;
    }

    public static function getAdmSearchCate($pid = 0, $notIds = [], &$result = [], $num = -1)
    {
        $cate = self::filterWhere([
            ['status', '>=', 0],
            ['parent_id', '=', $pid],
            ['id', 'not in', $notIds],
        ])
            ->field('id,title')
            ->order('sort desc,id asc')
            ->select();

        $num++;
        foreach ($cate as $v) {
            $result['id-' . $v['id']] = $num == 0 ? $v['title'] : '|' . str_repeat('---', $num) . $v['title'];
            self::getAdmSearchCate($v['id'], $notIds, $result, $num);
        }

        return $result;
    }


    /**
     * 获取所有子分类Id
     */
    public static function getSubIds($pid = 0, &$ids = [])
    {
        $ids[] = $pid;
        $cate  = self::filterWhere([['status', '>=', 0], ['parent_id', '=', $pid]])
            ->field('id,title')
            ->order('sort desc,id asc')
            ->select();

        foreach ($cate as $v) {
            $ids[] = $v['id'];
            self::getSubIds($v['id'], $ids);
        }

        return array_values(array_unique($ids));
    }

    public static function getParentList($cate, &$crumbs = [])
    {
        $crumbs[] = $cate;

        if ($cate->parent_id != 0) {
            self::getParentList($cate->parent, $crumbs);
        }

        return array_reverse($crumbs);
    }

    public function getNews()
    {
        $newsIds = explode(',', $this->news_ids);

        return News::filterWhere([['id', 'in', $newsIds]])
            ->select();
    }
}

