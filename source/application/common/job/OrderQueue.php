<?php

namespace app\common\job;

use app\api\model\User;
use app\common\exception\BaseException;
use app\common\model\PointsCaptial;
use think\queue\Job as JobModel;

class OrderQueue
{
    public function fire(JobModel $job,$data){
        if($this->dealWith($data)){
//            $job->delete();
        } else {
            $job->release(1);
            echo "操作成功";
        }
    }

    public function dealWith($data){
        $model = new PointsCaptial();
        $userModel = new User();
        $todayData = $model
//            ->whereTime("create_time", "today")
            ->where("type", "not in","10")
            ->select();
        foreach ($todayData as $value) {
            $info = $userModel->where("user_id", $value["user_id"])->find();
            $userModel->startTrans();
            try {
                if($info["points"] >= $value["points"]) {
                    $userModel->where("user_id", $value["user_id"])
                        ->setDec("points", $value["points"]);
                    $userModel->where("user_id", $value["user_id"])
                        ->setInc("freeze_points", $value["points"]);
                    echo "准备扣除" . $value["points"] . "，现在积分是" . $info["points"] . "\r\n";
                } else {
                    if($info["points"] !== 0) {
                        $userModel->where("user_id", $value["user_id"])
                            ->setDec("points", $info["points"]);
                        $userModel->where("user_id", $value["user_id"])
                            ->setInc("freeze_points", $info["points"]);
                        echo "准备扣除" . $value["points"] . "，现在积分是" . $info["points"] . "\r\n";
                    }
                }
                $userModel->commit();
            } catch (BaseException $exception) {
                echo $exception->getMessage() . "\r\n";
            }
        }
        return true;
    }
}