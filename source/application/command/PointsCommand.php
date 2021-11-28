<?php

namespace app\command;

use app\api\model\Goods;
use app\api\model\Order;
use app\api\model\UserReferee;
use app\common\exception\BaseException;
use app\common\model\PointsCaptial;
use app\common\model\User;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Queue;

class PointsCommand extends Command
{
    protected $output = null;

    protected function configure()
    {
        $this->setName("points")->setDescription("每日释放积分");
    }

    protected function execute(Input $input, Output $output)
    {
        $this->output = $output;
//        $output->writeln('任务开始');
        $this->bonus();
    }

    public function bonus(){
//        $this->output->writeln("开始释放积分");
        // 首先计算团队并且更新数据
        $model = new User();
        $refereeModel = new UserReferee();
        $pointsCapitalModel = new PointsCaptial();
        $orderModel = new Order();
        $allUser = $model
            ->where("points",">", 0)
            ->select();
        $model->startTrans();
        try {
            // 首次释放0.003
            foreach ($allUser as $value) {
                // 每日释放千分之3
                $thousand = bcmul($value["all_points"], 0.003, 2);
                // 上拿一代
                $bottomFirstDirectPush = $refereeModel
                    ->where("dealer_id", $value["user_id"])
                    ->where("level", 1)
                    ->select();
                $bottomFirstDirectPushPeopleCount = 0;
                foreach ($bottomFirstDirectPush as $haveCount) {
                    $info = $model->where("user_id", $haveCount["user_id"])
                        ->where("points", ">", "0")->find();
                    if (!empty($info)) {
                        $bottomFirstDirectPushPeopleCount++;
                    }
                }
                $mySelfPercent20 = bcmul($thousand, 0.2, 2);
                if ($bottomFirstDirectPushPeopleCount == 0) {
                    $divFirstDirectPushPoints = 0;
                } else {
                    $divFirstDirectPushPoints = bcdiv($mySelfPercent20, $bottomFirstDirectPushPeopleCount, 2);
                }
                $info = $model->where("user_id", $value["user_id"])->find();
                if ($divFirstDirectPushPoints > 0) {
                    foreach ($bottomFirstDirectPush as $directPushValue) {
                        // 开始分配上拿积分
                        $pointsCapitalModel->insert([
                            "order_id" => 0,
                            "type" => 40,
                            "user_id" => $directPushValue["user_id"],
                            "points" => $divFirstDirectPushPoints,
                            "description" => "上拿一代{$info['username']}的加速释放",
                            "consignment_money" => 0,
                            "is_delete" => 0,
                            "wxapp_id" => 10001,
                            "create_time" => time()
                        ]);
                    }
                }
                $pointsCapitalModel->insert([
                    "order_id" => 0,
                    "type" => 30,
                    "user_id" => $value["user_id"],
                    "points" => $thousand,
                    "description" => "每日静态释放",
                    "consignment_money" => 0,
                    "is_delete" => 0,
                    "wxapp_id" => 10001,
                    "create_time" => time()
                ]);
//                $this->output->writeln("【" . $value["username"] . "<{$value['user_id']}>的静态释放为：" . $thousand . "】");
                // 开始查询下拿一代是否有报单
                $first = $refereeModel
                    ->where("dealer_id", $value["user_id"])
                    ->with(['user'])
                    ->where("level", 1)
                    ->select();
                $firstTotalPoints = 0;
                $name_str = "";
                foreach ($first as $firstValue) {
                    // 检测是否有积分
                    if ($firstValue["user"]["points"] > 0) {
                        // 有积分接着操作
                        $firstThousand = bcmul($firstValue["user"]["all_points"], 0.003, 2);
                        $firstTotalPoints = $firstThousand + $firstTotalPoints;
                        $name_str .= $firstValue["user"]["username"] . ",";
                    }
                }
                //下拿一代的30%
                if ($firstTotalPoints > 0) {
                    $percent30 = bcmul($firstTotalPoints, 0.3, 2);
                    $name_str = substr($name_str, 0, strlen($name_str) - 1);;
                    $pointsCapitalModel->insert([
                        "order_id" => 0,
                        "type" => 40,
                        "user_id" => $value["user_id"],
                        "points" => $percent30,
                        "description" => "下拿一代{$name_str}的加速释放",
                        "consignment_money" => 0,
                        "is_delete" => 0,
                        "wxapp_id" => 10001,
                        "create_time" => time()
                    ]);
                }

                // 开始查询二代是否有报单
                $second = $refereeModel
                    ->where("dealer_id", $value["user_id"])
                    ->with(['user'])
                    ->where("level", 2)
                    ->select();
                $secondTotalPoints = 0;
                $name_str = "";
                foreach ($second as $firstValue) {
                    // 检测是否有积分
                    if ($firstValue["user"]["points"] > 0) {
                        // 有积分接着操作
                        $secondThousand = bcmul($firstValue["user"]["all_points"], 0.003, 2);
                        $secondTotalPoints = $secondThousand + $secondTotalPoints;
                        $name_str .= $firstValue["user"]["username"] . ",";
                    }
                }
                // 下拿二代的50%
                if ($secondTotalPoints > 0) {
                    $percent50 = bcmul($secondTotalPoints, 0.5, 2);
                    $name_str = substr($name_str, 0, strlen($name_str) - 1);;
                    $pointsCapitalModel->insert([
                        "order_id" => 0,
                        "type" => 40,
                        "user_id" => $value["user_id"],
                        "points" => $percent50,
                        "description" => "下拿二代{$name_str}的加速释放",
                        "consignment_money" => 0,
                        "is_delete" => 0,
                        "wxapp_id" => 10001,
                        "create_time" => time()
                    ]);
                }
            }

            // 查找出所有的尾节点(leaf node)
            $allLeafUser = $model
                ->where("is_leaf", 1)
                ->select();
            foreach ($allLeafUser as $leaf) {
                // 查找出所有上级
                $GLOBALS['allParentUserIds'] = [];
                $this->getTopLine($leaf["user_id"]);
                $data = $GLOBALS['allParentUserIds'];
                foreach ($data as $value) {
                    $this->repeatDealWith($value);
                }
            }

//            $allUser = $model
//                ->where("points", ">", 0)
////                ->where("level", ">", 0)
//                ->select();
//            foreach ($allUser as $allUserValue) {
////                echo "------------------------------------------\r\n";
//                $levelGrade = [0,0.2,0.4,0.6,0.9,1.2];
//                $GLOBALS['all_user'] = [];
//                $this->getFirst($allUserValue["user_id"]);
//                $user_ids = "";
//                foreach ($GLOBALS['all_user'] as $value) {
//                    $user_ids .= $value["user_id"] . ",";
//                }
//                $user_ids .= $allUserValue["user_id"];
//                $allPoints = $model
//                    ->where("user_id", "in", $user_ids)
//                    ->sum("all_points");
//                $thousand_3 = bcmul($allPoints, 0.003,2);
//
//                // 查找上级
//                $topUserId = $refereeModel
//                    ->where("user_id", $allUserValue["user_id"])
//                    ->where("level",1)
//                    ->find();
//                // 看上级是什么级别
//                $topUserInfo = $model
//                    ->where("user_id", $topUserId["dealer_id"])
//                    ->find();
//                $topUserLevel = $topUserInfo["level"];
////                    dump($allUserValue["level"]);
//                $myLevel = $allUserValue["level"];
////                    dump($myLevel);
////                    dump($topUserLevel);
////                echo $allUserValue["username"] . "的上级如下";
//                $GLOBALS['allParentUserIds'] = [];
//                $this->getTopLine($allUserValue["user_id"]);
//                $topUserData = $GLOBALS['allParentUserIds'];
//                $topUserNewData = [];
//                foreach ($topUserData as $vv) {
//                    $info = $model->where("user_id", $vv)
//                        ->find();
//                    $topUserNewData[] = [
//                        "user_id" => $vv,
//                        "level" => $info["level"],
//                        "username" => $info["username"]
//                    ];
//                }
//                if($myLevel < $topUserInfo["level"]) {
////                        echo $allUserValue["username"] . "的千分之3是" . $thousand_3 . "\r\n";
//                    $percent = bcsub($levelGrade[$topUserInfo["level"]], $levelGrade[$myLevel], 2);
//                    $fee = bcmul($thousand_3, $percent, 2);
//                    $pointsCapitalModel->insert([
//                        "order_id" => 0,
//                        "type" => 50,
//                        "user_id" => $topUserInfo["user_id"],
//                        "points" => $fee,
//                        "description" => $topUserInfo["username"] . "拿" . $allUserValue["username"] . "的级差团队的加速释放，极差的百分比是：" . $percent,
//                        "consignment_money" => 0,
//                        "is_delete" => 0,
//                        "wxapp_id" => 10001,
//                        "create_time" => time()
//                    ]);
//                    echo $topUserInfo["username"] . "拿" . $allUserValue["username"]
//                        .
//                        "的级差团队的加速释放，极差的百分比是："
//                        .
//                        $percent . "积分是：" . $fee . "\r\n";
////                        dump([
////                            "order_id" => 0,
////                            "type" => 50,
////                            "user_id" => $thouData["user_id"],
////                            "points" => $fee,
////                            "description" => $thouData["username"] . "拿" . $allUserValue["username"] . "的级差团队的加速释放，极差的百分比是：" . $percent,
////                            "consignment_money" => 0,
////                            "is_delete" => 0,
////                            "wxapp_id" => 10001,
////                            "create_time" => time()
////                        ]);
//                }
//            }
//            $jobHandlerClassName = 'app\common\job\OrderQueue';
//            $jobQueueName = "OrderDealWith";
//            $jobData = ["time" => "today"];
//            Queue::later(1, $jobHandlerClassName, $jobData, $jobQueueName);
//            $model->commit();
        } catch (BaseException $exception){
            $this->output->writeln($exception->getMessage());
        }
    }

    public function repeatDealWith($user_id){
        $model = new Order();
        $userModel = new User();
        $pointsCapitalModel = new PointsCaptial();
        $leaf = $userModel->where("user_id", $user_id)
            ->find();
        // 定义级别
        $levelGrade = [0,0.2,0.4,0.6,0.9,1.2];
        // 查找出子节点上面的所有人
        $GLOBALS['allParentUserIds'] = [];
        $this->getTopLine($user_id);
        $data = $GLOBALS['allParentUserIds'];
        // 循环遍历所有的父节点
        if(!empty($data)) {
            $newParent = [];
            foreach ($data as $parent) {
                // 把有级别的父节点放在新数组中
                $info = $userModel->where("user_id", $parent)
                    ->find();
                if($info["level"] > 0){
                    // 把有级别的父亲节点都找出来
                    $newParent[] = [
                        "user_id" => $info["user_id"],
                        "username" => $info["username"],
                        "level" => $info["level"],
                        "is_leaf" => $info["is_leaf"]
                    ];
                }
            }
            // 查找出自己的全部积分
            $allPoints = $userModel
                ->where("user_id", $leaf["user_id"])
                ->value("all_points");
            // 计算自己全部积分的千分之3
            $thousand_3 = bcmul($allPoints, 0.003,2);
            foreach ($newParent as $newParentInfo){
                if($newParentInfo["level"] == 5){
                    break;
                }
                if($leaf["level"] !== -1){
                    if($leaf["level"] < $newParentInfo["level"]) {
//                            echo $allUserValue["username"] . "的千分之3是" . $thousand_3 . "\r\n";
                        $percent = bcsub($levelGrade[$newParentInfo["level"]], $levelGrade[$leaf["level"]], 2);
                        if($thousand_3 == 0){
                            continue;
                        }
                        $fee = bcmul($thousand_3, $percent, 2);
                        $pointsCapitalModel->insert([
                            "order_id" => 0,
                            "type" => 50,
                            "user_id" => $newParentInfo["user_id"],
                            "points" => $fee,
                            "description" => $newParentInfo["username"] . "拿" . $leaf["username"] . "的级差团队的加速释放，极差的百分比是：" . $percent,
                            "consignment_money" => 0,
                            "is_delete" => 0,
                            "wxapp_id" => 10001,
                            "create_time" => time()
                        ]);
                        echo $newParentInfo["username"] . "拿" . $leaf["username"]
                            .
                            "的级差团队的加速释放，极差的百分比是："
                            .
                            $percent . "积分是：" . $fee . "\r\n";
                    }
                    if ($leaf["level"] == $newParentInfo["level"]) {
                        $newParentUserInfo = $userModel->where("user_id", $newParentInfo["user_id"])
                            ->find();
                        $sameLevelThousands = bcmul($newParentUserInfo["all_points"], 0.003, 2);
                        // 拿下级的20%
                        $percent20Points = bcmul($sameLevelThousands, 0.2, 2);
                        if($percent20Points <= 0){
                            continue;
                        }
//                        $pointsCapitalModel->insert([
//                            "order_id" => 0,
//                            "type" => 50,
//                            "user_id" => $newParentUserInfo["user_id"],
//                            "points" => $percent20Points,
//                            "description" => $newParentUserInfo["username"] . "拿" . $leaf["username"] . "平级团队的加速释放",
//                            "consignment_money" => 0,
//                            "is_delete" => 0,
//                            "wxapp_id" => 10001,
//                            "create_time" => time()
//                        ]);
                        echo "----------------------------\r\n";
                        echo $newParentUserInfo["username"] . "拿" . $leaf["username"] . "平级团队的加速释放，积分是：" . $percent20Points ."\r\n";
                    }
                }
            }
        }
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

    /**
     * 检测用户是否购买且支付过报单商品
     * @param $user_id
     */
    public function checkUserStatus($user_id){
        $model = new Order();
        $goodsModel = new Goods();
        $data = $model->where("user_id", $user_id)
            ->where("pay_status", 20)
            ->select();
        $isHaveMemberMallGoods = false;
        foreach($data as $value) {
            $goods_id = $value["goods"][0]["goods_id"];
            $goodsInfo = $goodsModel->where("goods_id", $goods_id)
                ->find();
            if($goodsInfo["category_id"] == 10001) {
                $isHaveMemberMallGoods = true;
            }
        }
        return $isHaveMemberMallGoods;
    }

    public function checkTeamFirstGrade($user_id){
        $model = new Order();
        $GLOBALS['all_user'] = [];
        $this->getFirst($user_id);
        $myTeamData = $GLOBALS['all_user'];
        $user_ids = "";
        foreach ($myTeamData as $myTeamDataValue) {
            $user_ids .= $myTeamDataValue['user_id'] . ",";
        }
        $user_ids = substr($user_ids, 0 ,strlen($user_ids)-1);
        $totalPerformance = $model
            ->where("user_id", "in", $user_ids)
            ->sum("pay_price");
        if($totalPerformance >= 200000) {
            return true;
        }
        return false;
    }

    public function checkTeamSecondGrade($user_id){
        $model = new Order();
        $refereeModel = new UserReferee();
        $GLOBALS['all_user'] = [];
        $this->getFirst($user_id);
        $myTeamData = $GLOBALS['all_user'];
        $user_ids = "";
        foreach ($myTeamData as $myTeamDataValue) {
            $user_ids .= $myTeamDataValue['user_id'] . ",";
        }
        $user_ids = substr($user_ids, 0 ,strlen($user_ids)-1);
        $totalPerformance = $model
            ->where("user_id", "in", $user_ids)
            ->sum("pay_price");
        if($totalPerformance >= 800000) {
            $v1Count = 0;
            $directPush = $refereeModel->where("dealer_id", $user_id)
                ->where("level", 1)
                ->select();
            foreach ($directPush as $directPushValue) {
                if($this->checkTeamFirstGrade($directPushValue["user_id"])){
                    $v1Count++;
                }
            }
            if($v1Count >= 2){
                return true;
            }
            return false;
        }
        return false;
    }

    public function checkTeamThirdGrade($user_id) {
        $model = new Order();
        $refereeModel = new UserReferee();
        $GLOBALS['all_user'] = [];
        $this->getFirst($user_id);
        $myTeamData = $GLOBALS['all_user'];
        $user_ids = "";
        foreach ($myTeamData as $myTeamDataValue) {
            $user_ids .= $myTeamDataValue['user_id'] . ",";
        }
        $user_ids = substr($user_ids, 0 ,strlen($user_ids)-1);
        $totalPerformance = $model
            ->where("user_id", "in", $user_ids)
            ->sum("pay_price");
        if($totalPerformance >= 2000000) {
            $v2Count = 0;
            $dirEctPushThird = $refereeModel->where("dealer_id", $user_id)
                ->where("level", 1)
                ->select();
            foreach ($dirEctPushThird as $thirdEctValue) {
                if($this->checkTeamSecondGrade($thirdEctValue["user_id"])){
                    $v2Count++;
                }
            }
            if($v2Count == 2){
                return true;
            }
            return false;
        }
        return false;
    }

    public function checkTeamFourGrade($user_id) {
        $model = new Order();
        $refereeModel = new UserReferee();
        $GLOBALS['all_user'] = [];
        $this->getFirst($user_id);
        $myTeamData = $GLOBALS['all_user'];
        $user_ids = "";
        foreach ($myTeamData as $myTeamDataValue) {
            $user_ids .= $myTeamDataValue['user_id'] . ",";
        }
        $user_ids = substr($user_ids, 0 ,strlen($user_ids)-1);
        $totalPerformance = $model
            ->where("user_id", "in", $user_ids)
            ->sum("pay_price");
        if($totalPerformance >= 5000000) {
            $v3Count = 0;
            $dirEctPushThird = $refereeModel->where("dealer_id", $user_id)
                ->where("level", 1)
                ->select();
            foreach ($dirEctPushThird as $thirdEctValue) {
                if($this->checkTeamThirdGrade($thirdEctValue["user_id"])){
                    $v3Count++;
                }
            }
            if($v3Count == 2){
                return true;
            }
            return false;
        }
        return false;
    }

    public function checkTeamFiveGrade($user_id) {
        $model = new Order();
        $refereeModel = new UserReferee();
        $GLOBALS['all_user'] = [];
        $this->getFirst($user_id);
        $myTeamData = $GLOBALS['all_user'];
        $user_ids = "";
        foreach ($myTeamData as $myTeamDataValue) {
            $user_ids .= $myTeamDataValue['user_id'] . ",";
        }
        $user_ids = substr($user_ids, 0 ,strlen($user_ids)-1);
        $totalPerformance = $model
            ->where("user_id", "in", $user_ids)
            ->sum("pay_price");
        if($totalPerformance >= 12000000) {
            $v4Count = 0;
            $dirEctPushThird = $refereeModel->where("dealer_id", $user_id)
                ->where("level", 1)
                ->select();
            foreach ($dirEctPushThird as $thirdEctValue) {
                if($this->checkTeamFourGrade($thirdEctValue["user_id"])){
                    $v4Count++;
                }
            }
            if($v4Count == 2){
                return true;
            }
            return false;
        }
        return false;
    }

    public function checkTeamGrade($user_id){
        $level = 0;
        if($this->checkTeamFirstGrade($user_id)) {
            $level = 1;
        }
        if($this->checkTeamSecondGrade($user_id)) {
            $level = 2;
        }
        if($this->checkTeamThirdGrade($user_id)) {
            $level = 3;
        }
        if($this->checkTeamFourGrade($user_id)) {
            $level = 4;
        }
        if($this->checkTeamFiveGrade($user_id)) {
            $level = 5;
        }
        $userModel = new User();
        $userModel->where("user_id", $user_id)->update([
            "level" => $level
        ]);
        return $level;
    }

    public function getUserName($user_id){
        $model = new User();
        return $model->where("user_id", $user_id)->value("username");
    }

    /**
     * 计算指定等级的数量
     */
    public function checkLevelCount(){

    }

    public function dealWithUser($self_user_id, $user_id, $type){
        // 计算用户的千分之3
        $model = new User();
        $pointsCapitalModel = new PointsCaptial();
        $data = $model->where("user_id", $user_id)->find();
        if(!empty($data)){
            $thousandth_three = bcmul($data["points"], 0.003, 2);
            // 上拿
            // 20%的释放给到自己
            $percent20 = bcmul($thousandth_three, 0.2, 2);
            $model->where("user_id", $self_user_id)->setDec("points", $percent20);
            $model->where("user_id", $self_user_id)->setInc("freeze_points", $percent20);
            $pointsCapitalModel->insert([
                "order_id" => 0,
                "type" => 40,
                "user_id" => $self_user_id,
                "points" => $percent20,
                "description" => "上拿一代{$data['username']}的加速释放",
                "consignment_money" => 0,
                "is_delete" => 0,
                "wxapp_id" => 10001,
                "create_time" => time()
            ]);
        }
    }
}