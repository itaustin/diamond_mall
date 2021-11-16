<?php

namespace app\common\job;

use app\api\model\User;
use app\api\model\UserReferee;
use app\common\model\PointsCaptial;
use think\queue\Job as JobModel;

class SameLevel
{
    public function fire(JobModel $job,$data){
        if($this->dealWith($data)){
            $job->delete();
        } else {
            $job->release(1);
            echo "操作成功";
        }
    }

    public function dealWith($data) {
        $model = new User();
        $refereeModel = new UserReferee();
        $pointsCapitalModel = new PointsCaptial();
        // 统计用户下面的业绩
        // 检测用户的等级
        $user_id = $data['user_id'];
        $data = $model->where("user_id", $user_id)->find();
        $level = $data["level"];
        if($level == -1 || $level == 0){
            echo "等级不足走同级业务体系";
            return true;
        } else {
            $levelGrade = [0,0.2,0.4,0.6,0.9,1.2];
            $GLOBALS['all_user'] = [];
            $this->getFirst($user_id);
            $user_ids = "";
            foreach ($GLOBALS['all_user'] as $refereeInfo) {
                $user_ids .= $refereeInfo["user_id"] . ",";
            }
            $allPoints = $model->where("user_id", "in", $user_ids)
                ->sum("points");
            echo "------------------------------------------------\r\n";
            echo "总业绩：" . $allPoints . "\r\n";
            $thousand = bcmul($allPoints, 0.003, 2);
            echo "千分之3：" . $thousand . "\r\n";

            // 查找我的上级
            $superior = $refereeModel
                ->where("user_id", $user_id)
                ->value('dealer_id');
            // 跟上级做等级对比
            $superiorInfo = $model->where("user_id", $superior)
                ->find();
            if($level > 0) {
                $mySelfPoints = bcmul($thousand, $levelGrade[$level], 2);
                $pointsCapitalModel->insert([
                    "order_id" => 0,
                    "type" => 50,
                    "user_id" => $user_id,
                    "points" => $mySelfPoints,
                    "description" => "拿平级团队的加速释放",
                    "consignment_money" => 0,
                    "is_delete" => 0,
                    "wxapp_id" => 10001,
                    "create_time" => time()
                ]);
                if($superiorInfo["level"] == $level) {
                    $superiorPoints = bcmul($mySelfPoints, $levelGrade[$superiorInfo["level"]], 2);
                    $pointsCapitalModel->insert([
                        "order_id" => 0,
                        "type" => 50,
                        "user_id" => $superiorInfo["user_id"],
                        "points" => $superiorPoints,
                        "description" => "拿平级团队的加速释放",
                        "consignment_money" => 0,
                        "is_delete" => 0,
                        "wxapp_id" => 10001,
                        "create_time" => time()
                    ]);
                    echo "------------------------------------------------\r\n";
                } else {
                    echo "业务逻辑不成体系，无法进行正常平级释放";
                }
            }
            if($superiorInfo["level"] == $level) {
                return true;
            }
        }
        return true;
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