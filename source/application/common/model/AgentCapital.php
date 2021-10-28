<?php
namespace app\common\model;

class AgentCapital extends BaseModel{
    /**
     * 分销商资金明细
     * @param $data
     */
    public static function add($data)
    {
        $model = new static;
        $model->save(array_merge([
            'wxapp_id' => $model::$wxapp_id
        ], $data));
    }
}