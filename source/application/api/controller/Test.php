<?php

namespace app\api\controller;

use app\api\model\UserReferee;

class Test extends Controller
{
    public function index(){
        $model = new \app\api\model\User();
        $refereeModel = new UserReferee();
        $allUser = $model
            ->where("points", ">", 0)
//            ->where("user_id", "10004")
            ->select();
        foreach ($allUser as $allUserValue) {
            echo "------------------------------------------<br/>";
            $levelGrade = [0,0.2,0.4,0.6,0.9,1.2];
            $GLOBALS['all_user'] = [];
            $this->getFirst($allUserValue["user_id"]);
            $user_ids = "";
            foreach ($GLOBALS['all_user'] as $value) {
                $user_ids .= $value["user_id"] . ",";
            }
            $user_ids .= $allUserValue["user_id"];
            $allPoints = $model
                ->where("user_id", "in", $user_ids)
                ->sum("all_points");
//            dump($allPoints);
            $thousand_3 = bcmul($allPoints, 0.003,2);

            // 查找上级
            $topUserId = $refereeModel
                ->where("user_id", $allUserValue["user_id"])
                ->where("level",1)
                ->find();
            // 看上级是什么级别
            $topUserInfo = $model
                ->where("user_id", $topUserId["dealer_id"])
                ->find();
            $topUserLevel = $topUserInfo["level"];
//                    dump($allUserValue["level"]);
            $myLevel = $allUserValue["level"];
//                    dump($myLevel);
//                    dump($topUserLevel);
            if($myLevel < $topUserLevel) {
                $percent = bcsub($levelGrade[$topUserLevel], $levelGrade[$myLevel], 2);
                $fee = bcmul($thousand_3, $percent, 2);
                dump([
//                    "order_id" => 0,
//                    "type" => 50,
                    "user_id" => $topUserId["dealer_id"],
                    "points" => $fee,
//                    "description" => "拿级差团队的加速释放",
//                    "consignment_money" => 0,
//                    "is_delete" => 0,
//                    "wxapp_id" => 10001,
//                    "create_time" => date("Y-m-d H:i:s")
                ]);
//                $pointsCapitalModel->insert([
//                    "order_id" => 0,
//                    "type" => 50,
//                    "user_id" => $topUserId["dealer_id"],
//                    "points" => $fee,
//                    "description" => "拿级差团队的加速释放",
//                    "consignment_money" => 0,
//                    "is_delete" => 0,
//                    "wxapp_id" => 10001,
//                    "create_time" => time()
//                ]);
            } else {
                echo "暂无数据<br/>";
            }
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