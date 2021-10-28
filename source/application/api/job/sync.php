<?php


namespace app\api\job;

use app\api\model\User;
use app\common\model\dealer\Capital;
use think\queue\Job as JobModel;


class sync
{
    public function fire(JobModel $job,$data){
        if($this->marketDividend($data['orderParam'])){
            echo "大盘分红结束\r\n";
        }
    }

    public function marketDividend($data){
        $marketPrice = bcmul($data['order_price'], bTon(3), 2);
        $userModel = new User();
        $data = $userModel->alias("user")
            ->where("market_index",">",0)
            ->join("dealer_user","user.user_id=dealer_user.user_id")
            ->field("user.user_id,user.nickName,user.market_index")
            ->select();
        $total_market_index = 0;
        foreach ($data as $key => $value){
            $total_market_index += $value['market_index'];
        }
        // 每分多少钱
        $oneScorePrice = bcdiv($marketPrice,$total_market_index,2);
        $dealerModel = new \app\api\model\dealer\User();
        foreach ($data as $value){
            // 记录分销商资金明细
            Capital::add([
                'user_id' => $value['user_id'],
                'flow_type' => 10,
                'money' => bcmul($oneScorePrice,$value['market_index'],2),
                'describe' => '大盘分红',
                'wxapp_id' => 10001,
            ]);
            $dealerModel->where("user_id",$value['user_id'])->setInc("money",bcmul($oneScorePrice,$value['market_index'],2));
        }
        return true;
    }
}