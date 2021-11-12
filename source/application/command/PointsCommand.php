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
        $output->writeln('任务开始');
        $this->bonus();
    }

    public function bonus(){
        $this->output->writeln("开始释放积分");
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
            foreach ($allUser as $k => $value){
                // 每日释放千分之3
                $thousand = bcmul($value["points"], 0.003, 2);
                $model->where("user_id", $value["user_id"])
                    ->setDec("points", $thousand);
                $model->where("user_id", $value["user_id"])
                    ->setInc("freeze_points", $thousand);
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
                $this->output->writeln("【" . $value["username"] . "<{$value['user_id']}>的静态释放为：" . $thousand . "】");
                // 开始计算他个人的上拿一代和下拿两代
                // 上拿一代
                $inviteUser = $refereeModel
                    ->where("user_id", $value["user_id"])
                    ->where("level", 1)
                    ->find()["dealer_id"];
                // 开始查询上拿一代是否有报单
                if($inviteUser) {
                    $parentStatus = $this->checkUserStatus($inviteUser);
                    if($parentStatus){
//                        $this->output->writeln($value["user_id"] . "的邀请人是：" . $first);
                        $this->dealWithUser($value["user_id"], $inviteUser, 0);
                    }
                }
                // 开始查询下拿一代是否有报单
//                if($first) {
//                    $firstStatus = $this->checkUserStatus($first);
//                    if($firstStatus){
//                        $this->output->writeln($value["user_id"] . "的直推人是：" . $first);
//                        $this->dealWithUser($value["user_id"], $first, 1);
//                    }
//                }
                $first = $refereeModel
                    ->where("dealer_id", $value["user_id"])
                    ->with(['user'])
                    ->where("level", 1)
                    ->select();
                $firstTotalPoints = 0;
                $name_str = "";
                foreach ($first as $firstValue){
                    // 检测是否有积分
                    if($firstValue["user"]["points"] > 0) {
                        // 有积分接着操作
                        $firstThousand = bcmul($firstValue["user"]["points"], 0.003, 2);
                        $firstTotalPoints = $firstThousand + $firstTotalPoints;
                        $name_str .= $firstValue["user"]["username"] . ",";
                    }
                }
                // 下拿一代的30%
                if($firstTotalPoints > 0) {
                    $percent30 = bcmul($firstTotalPoints, 0.3, 2);
                    $model->where("user_id", $value["user_id"])->setDec("points", $percent30);
                    $model->where("user_id", $value["user_id"])->setInc("freeze_points", $percent30);
                    $name_str = substr($name_str,0,strlen($name_str)-1);;
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
                foreach ($second as $firstValue){
                    // 检测是否有积分
                    if($firstValue["user"]["points"] > 0) {
                        // 有积分接着操作
                        $secondThousand = bcmul($firstValue["user"]["points"], 0.003, 2);
                        $secondTotalPoints = $secondThousand + $secondTotalPoints;
                        $name_str .= $firstValue["user"]["username"] . ",";
                    }
                }
                // 下拿二代的50%
                if($secondTotalPoints > 0) {
                    $percent50 = bcmul($secondTotalPoints, 0.5, 2);
                    $model->where("user_id", $value["user_id"])->setDec("points", $percent50);
                    $model->where("user_id", $value["user_id"])->setInc("freeze_points", $percent50);
                    $name_str = substr($name_str,0,strlen($name_str)-1);;
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

                // 开始计算团队
                // 计算自己是什么等级
                $this->checkTeamGrade($value["user_id"]);
            }
            // 查询所有订单
            $allOrder = $orderModel
                ->whereTime("create_time", "yesterday")
                ->where("pay_status", 20)
                ->select();
            foreach ($allOrder as $orderInfo) {
                // 上找所有人
                $GLOBALS['allParentUserIds'] = [];
                $this->getTopLine($orderInfo["user_id"]);
                $refereeLineData = $GLOBALS['allParentUserIds'];
                $new_mark = 0;
                $position = 0;
                $topPercent = 0;
                foreach ($refereeLineData as &$refereeUser) {
                    if($position == 3){
                        break;
                    }
                    $level = $this->checkTeamGrade($refereeUser);
                    $percent = 0;
                    if($level == 5) {
                        $percent = 1.2;
                    } else if($level == 4) {
                        $percent = 0.9;
                    } else if($level == 3){
                        $percent = 0.6;
                    } else if($level == 2){
                        $percent = 0.4;
                    } else if($level == 1) {
                        $percent = 0.2;
                    } else if($level == 0) {
                        $percent = 0;
                    }
                    $points = $model->where("user_id", $refereeUser)->value("points");
                    if($level > 0){
                        if($new_mark > $level){
                            // 拿该拿的剩下的
                            $thousand = bcmul($orderInfo["pay_price"], 0.003, 2);
                            $percentPoints = bcmul($topPercent, $percent, 2);
                            // 拿自己的减去上一次的
                            $mySelfPoints = $percentPoints - $topPercent;
                            // 检测自己积分，积分不足就不释放了
                            if($points > 0){
                                $model->where("user_id", $refereeUser)->setDec("points", $mySelfPoints);
                                $model->where("user_id", $refereeUser)->setInc("freeze_points", $mySelfPoints);
                                $name_str = substr($name_str,0,strlen($name_str)-1);;
                                $pointsCapitalModel->insert([
                                    "order_id" => 0,
                                    "type" => 50,
                                    "user_id" => $refereeUser,
                                    "points" => $percentPoints,
                                    "description" => "拿团队的加速释放",
                                    "consignment_money" => 0,
                                    "is_delete" => 0,
                                    "wxapp_id" => 10001,
                                    "create_time" => time()
                                ]);
                            }
                            $new_mark = $level;
                            $position++;
                            $topPercent = $percentPoints;
                        } else {
                            $thousand = bcmul($orderInfo["pay_price"], 0.003, 2);
                            if($topPercent > 0){
                                $percentPoints = bcmul($topPercent, $percent, 2);
                            } else {
                                $percentPoints = bcmul($thousand, $percent, 2);
                            }
                            if($points > 0){
                                $model->where("user_id", $refereeUser)->setDec("points", $percentPoints);
                                $model->where("user_id", $refereeUser)->setInc("freeze_points", $percentPoints);
                                $name_str = substr($name_str,0,strlen($name_str)-1);;
                                $pointsCapitalModel->insert([
                                    "order_id" => 0,
                                    "type" => 50,
                                    "user_id" => $refereeUser,
                                    "points" => $percentPoints,
                                    "description" => "拿团队的加速释放",
                                    "consignment_money" => 0,
                                    "is_delete" => 0,
                                    "wxapp_id" => 10001,
                                    "create_time" => time()
                                ]);
                            }
                            $new_mark = $level;
                            $position++;
                            $topPercent = $percentPoints;
                        }
                    }
                }
            }
            $model->commit();
        } catch (BaseException $exception){
            $this->output->writeln($exception->getMessage());
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

    public function checkTeamGrade($user_id){
        $model = new Order();
        $userModel = new User();
        $GLOBALS['all_user'] = [];
        $this->getFirst($user_id);
        $myTeamData = $GLOBALS['all_user'];
        $user_ids = "";
        foreach($myTeamData as $value) {
            $user_ids .= $value["user_id"] . ",";
        }
        $totalPerformance = $model
            ->where("user_id", "in", $user_ids)
            ->sum("pay_price");
        $level = -1;
        if($totalPerformance >= 200000){
            $level = 1;
        }
        if($totalPerformance >= 800000) {
            // 自己检测自己的直推有多少个V1，满足2个V1即可升级V2
            $level = 2;
        }
        if($totalPerformance >= 2000000) {
            // 自己检测自己的直推有多少个V2，满足2个V2即可升级V3
            $level = 3;
        }
        if($totalPerformance >= 5000000){
            // 自己检测自己的直推有多少个V3，满足2个V3即可升级V4
            $level = 4;
        }
        if($totalPerformance >= 12000000){
            // 自己检测自己的直推有多少个V4，满足2个V4即可升级V5
            $level = 5;
        }
        $userModel->where("user_id", $user_id)->update([
            "level" => $level
        ]);
//        $this->output->writeln($user_id . "升级了" . $level . "等级");
        return $level;
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
            switch($type){
                case 0:
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
                    break;
            }
        }
    }
}