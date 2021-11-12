<?php

namespace app\common\model;

class GoldCoupon extends BaseModel
{
    protected $autoWriteTimestamp = true;
    protected $createTime = "create_time";
    protected $updateTime = "update_time";

    public function user(){
        return $this->hasOne("app\api\model\User", "user_id", "user_id");
    }

    public function order(){
        return $this->hasOne("app\api\model\Order", "order_id", "order_id");
    }
}