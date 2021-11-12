<?php

namespace app\api\controller;

use app\common\model\GoldCoupon;

class Gold extends Controller
{
    public function getList(){
        $model = new GoldCoupon();
        $userInfo = $this->getUser();
        $data = $model
            ->with(['user','order'])
            ->where("user_id", $userInfo['user_id'])
            ->order("golden_coupon_id DESC")
            ->where("is_use", 0)
            ->select();
        return $this->renderSuccess($data,"success");
    }
}