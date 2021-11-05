<?php

namespace app\common\model;

/**
 * 商品规格关系模型
 * Class GoodsSpecRel
 * @package app\common\model
 */
class GoodsSpecRel extends BaseModel
{
    /**
     * 关联规格组
     * @return \think\model\relation\BelongsTo
     */
    public function spec()
    {
        return $this->belongsTo('Spec');
    }

}
