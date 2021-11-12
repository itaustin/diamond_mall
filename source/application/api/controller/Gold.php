<?php

namespace app\api\controller;

use app\common\exception\BaseException;
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

    public function clerk(){
        $coupon_id = input("golden_coupon_id");
        $user = $this->getUser();
        $userModel = new \app\api\model\User();
        $model = new GoldCoupon();
        $info = $model->where("golden_coupon_id", $coupon_id)
            ->find();
        if($info["is_use"] == 1){
            return $this->renderError("订单已经核销");
        } else {
            // 检测手工积分是否足够
            $need_points = $info["money"] * 68;
            if($user["handling_fee_points"] >= $need_points){
                $model->startTrans();
                try {
                    $model->where("golden_coupon_id", $coupon_id)
                        ->update("is_use", 1);
                    $userModel->where("user_id", $user_id)->setDec("handling_fee_points", $need_points);
                    $model->commit();
                    return $this->renderSuccess("","黄金券核销成功");
                } catch (BaseException $exception){
                    return $this->renderError($exception->getMessage(), "");
                }
            } else {
                return $this->renderError("您的手工积分不足","");
            }
        }
    }
}