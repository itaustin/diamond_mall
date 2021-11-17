<?php

namespace app\api\controller;

use app\api\model\UserReferee;
use think\Queue;

class Queuejob extends Controller
{
    public function job(){
        $jobHandlerClassName = 'app\common\job\OrderQueue';
        $jobQueueName = "OrderDealWith";
        $jobData = ["user_id" => "10029"];
        Queue::later(1, $jobHandlerClassName, $jobData, $jobQueueName);

//        $jobHandlerClassName = 'app\common\job\SameLevel';
//        $jobQueueName = "OrderSameLevel";
//        $jobData = ["user_id" => "10020"];
//        Queue::later(1, $jobHandlerClassName, $jobData, $jobQueueName);
    }

    /**
     * @description 获取推荐关系的一条线
     * @param $user_id
     */
    public function getTopLine($user_id){
        $model = new UserReferee();
        // 找到自己的上级
        $dealer_id = $model->where("user_id", $user_id)
            ->where("level", 1)
            ->value("dealer_id");
        if(!empty($dealer_id)){
            $GLOBALS['allParentUserIds'][] = $dealer_id;
            $this->getTopLine($dealer_id);
        }
    }

    public function getFirst($user_id)
    {
        $model = new UserReferee();
        $data = $model
            ->where("dealer_id", $user_id)
            ->where("level", 1)
            ->field("id,dealer_id, user_id")
            ->select();
        foreach ($data as $value) {
            $GLOBALS['all_user'][] = $value['users']->toArray();
            $this->getFirst($value["user_id"]);
        }
    }
}