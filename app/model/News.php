<?php

namespace app\model;

/**
 * @property int    $id
 * @property string $created_at
 * @property string $updated_at
 * @property int    $status
 *
 * @property int    $admin_id  @admin@
 * @property int    $cate_id   @cate@
 * @property int    $sort      排序
 * @property int    $type      类型
 * @property string $name      名称
 * @property string $city      地市
 * @property string $county    区县
 * @property string $address   地址
 * @property string $phone     电话
 * @property string $summary   简介
 * @property string $img_cover 封面图
 * @property string $img_bg    背景图
 * @property int    $is_rec    推荐
 * @property string $add_time  发布时间
 * @property int    $is_del    回收站
 *
 * @property Admin  $admin
 * @property Cate   $cate
 */
class News extends \app\model\AR
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'news';

    protected $schema = [
        'id'         => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status'     => 'int',
        'admin_id'   => 'int',
        'cate_id'    => 'int',
        'sort'       => 'int',
        'type'       => 'tinyint',
        'name'       => 'varchar',
        'city'       => 'varchar',
        'county'     => 'varchar',
        'address'    => 'varchar',
        'phone'      => 'varchar',
        'summary'    => 'varchar',
        'content'    => 'text',
        'img_cover'  => 'varchar',
        'img_bg'     => 'varchar',
        'is_rec'     => 'tinyint',
        'add_time'   => 'varchar',
        'is_del'     => 'tinyint',
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
        return $this->hasOne(Admin::class, 'id', 'admin_id');
    }

    public function cate()
    {
        return $this->hasOne(Cate::class, 'id', 'cate_id');
    }

    // ---------- Custom code below ----------

    public function gallery()
    {
        return $this->hasMany(NewsGallery::class, 'news_id', 'id');
    }

    const TYPE_NEWS = 1;
    const TYPE_DOWNLOAD = 0;

    const TYPE_TEXT = [
        self::TYPE_NEWS     => '新闻',
        self::TYPE_DOWNLOAD => '下载',
    ];

    public function getSolutionList()
    {
        return self::filterWhere([
            [
                'id',
                'in',
                $this->content,
            ],
        ])
            ->select();
    }

    public function getPartnerList()
    {
        return self::filterWhere([
            [
                'id',
                'in',
                $this->content2,
            ],
        ])
            ->select();
    }

    public function getInfo()
    {
        $info = $this->summary;
        if (empty($this->summary)) {
            $info = strip_tags($this->content);
        }
        if (empty($this->summary) && empty($this->content)) {
            $info = strip_tags($this->content2);
        }

        return $info;
    }

    public function createUrl()
    {
        return '/news.html?id=' . $this->id;
    }
}

