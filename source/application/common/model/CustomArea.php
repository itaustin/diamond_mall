<?php
namespace app\common\model;

use app\common\model\BaseModel;

class CustomArea extends BaseModel{
    /**
     * 自定义区域详情
     * @param $where
     * @param array $with
     * @return static|null
     * @throws \think\exception\DbException
     */
    public static function detail($where, $with = [])
    {
        !is_array($where) && $where = ['area_id' => (int)$where];
        return static::get(array_merge(['is_delete' => 0], $where), $with);
    }
}