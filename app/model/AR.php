<?php

namespace app\model;

class AR extends \shisou\tpgii\lib\AR
{
    /**
     * 查询列表, 带分页
     * @param array|null   $with   关联表
     * @param array|null   $where  查询条件
     * @param int|null     $page   页数
     * @param int|null     $limit  每页数
     * @param string|null  $order  查询排序
     * @param string|null   $fields 查询字段
     * @return array|AR[]
     */
    public function findList(
        array $with = null,
        array $where = null,
        int $page = 1,
        int $limit = 10,
        string $order = null,
        string $fields = null
    ) {
        $fields = is_null($fields) ? '*' : $fields;
        $where  = is_null($where) ? ['status' => 1] : $where;
        $order  = is_null($order) ? ['id' => SORT_DESC] : $order;

        $total = $this->filterWhere($where)->count();
        if ($with) {
            $list  = self::filterWhere($where)
                ->with($with)->field($fields)
                ->order($order)
                ->page($page, $limit)
                ->select()
                ->toArray();
        } else {
            $list  = $this->filterWhere($where)
                ->field($fields)
                ->order($order)
                ->page($page, $limit)
                ->select()
                ->toArray();
        }

        return [
            'total' => $total,
            'rows'  => $list
        ];
    }
}
