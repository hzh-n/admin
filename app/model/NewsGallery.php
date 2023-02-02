<?php

namespace app\model;

/**
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 * @property int $status
 *
 * @property int $sort 排序
 * @property int $news_id @news@
 * @property string $img_phone 单图
 * @property string $title 标题
 *
 * @property News $news
 */
class NewsGallery extends \app\model\AR 
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'news_gallery';

    protected  $schema = [
        'id'           => 'int',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'status'       => 'int',
        'sort'         => 'int', 
        'news_id'      => 'int', 
        'img_photo'    => 'varchar',
        'title'        => 'varchar', 
    ];


    public static function findOne($data = null): self
    {
        if (is_numeric($data)) {
            $data = ['id' => $data];
        }

        return self::where($data)
            ->find();
    }

    public function news()
    {
        return $this->hasOne(News::class, 'id' , 'news_id');
    }

    // ---------- Custom code below ----------
}

