<?php
/**
 * date: 2020/2/22 4:33 下午
 * @author 佐威网络科技-PaulAustin
 * @version v1.0
 * Description:
 */
namespace app\mobile\controller;

use app\api\model\OrderGoods;
use app\api\model\Order as OrderModel;

class Goods extends Controller{

    public function detail(){
        return $this->fetch("detail");
    }

    public function checkMallNo(){
        $uid = input('user_id');
        $firstMallCount = (new OrderGoods())->where("user_id",$uid)->field("order_id")->select();
        $countArr = [];
        foreach ($firstMallCount as $value){
            if($this->checkOrderPayStatus($value['order_id'])['value'] == 20){
                array_push($countArr,[$value]);
            }
        }
        if(count($countArr) > 0){
            return [
                'code' => 1,
            ];
        }else{
            return [
                'code' => 0,
            ];
        }
    }

    private function checkOrderPayStatus($order_id){
        $model = new OrderModel();
        return $model->where("order_id",$order_id)->field('pay_status')->find()['pay_status'];
    }

}