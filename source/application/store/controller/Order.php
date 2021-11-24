<?php

namespace app\store\controller;

use app\api\model\UserReferee;
use app\api\service\order\PaySuccess;
use app\common\enum\order\PayType as PayTypeEnum;
use app\common\exception\BaseException;
use app\common\model\GoldCoupon;
use app\common\model\PointsCaptial;
use app\common\model\User;
use app\store\model\Order as OrderModel;
use app\store\model\Express as ExpressModel;
use app\store\model\store\shop\Clerk as ShopClerkModel;
use app\store\model\store\Shop as ShopModel;

/**
 * 订单管理
 * Class Order
 * @package app\store\controller
 */
class Order extends Controller
{
    /**
     * 待发货订单列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function delivery_list()
    {
        return $this->getList('待发货订单列表', 'delivery');
    }

    /**
     * 待收货订单列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function receipt_list()
    {
        return $this->getList('待收货订单列表', 'receipt');
    }

    /**
     * 待付款订单列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function pay_list()
    {
        return $this->getList('待付款订单列表', 'pay');
    }

    /**
     * 已完成订单列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function complete_list()
    {
        return $this->getList('已完成订单列表', 'complete');
    }

    /**
     * 已取消订单列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function cancel_list()
    {
        return $this->getList('已取消订单列表', 'cancel');
    }

    /**
     * 全部订单列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function all_list()
    {
        return $this->getList('全部订单列表', 'all');
    }

    /**
     * 订单详情
     * @param $order_id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function detail($order_id)
    {
        // 订单详情
        $detail = OrderModel::detail($order_id);
        // 物流公司列表
        $expressList = ExpressModel::getAll();
        // 门店店员列表
        $shopClerkList = (new ShopClerkModel)->getList(true);
        return $this->fetch('detail', compact(
            'detail',
            'expressList',
            'shopClerkList'
        ));
    }

    /**
     * 确认发货
     * @param $order_id
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function delivery($order_id)
    {
        $model = OrderModel::detail($order_id);
        if ($model->delivery($this->postData('order'))) {
            return $this->renderSuccess('发货成功');
        }
        return $this->renderError($model->getError() ?: '发货失败');
    }

    /**
     * 修改订单价格
     * @param $order_id
     * @return array
     * @throws \think\exception\DbException
     */
    public function updatePrice($order_id)
    {
        $model = OrderModel::detail($order_id);
        if ($model->updatePrice($this->postData('order'))) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError($model->getError() ?: '修改失败');
    }

    /**
     * 订单列表
     * @param string $title
     * @param string $dataType
     * @return mixed
     * @throws \think\exception\DbException
     */
    private function getList($title, $dataType)
    {
        // 订单列表
        $model = new OrderModel;
        $list = $model->getList($dataType, $this->request->param());
        // 自提门店列表
        $shopList = ShopModel::getAllList();
        return $this->fetch('index', compact('title', 'dataType', 'list', 'shopList'));
    }

    public function audit($order_id){
        $model = new OrderModel();
        $orderInfo = $model::detail(["order_id", $order_id]);
        $model->startTrans();
        try {
            $model->where("order_id", $order_id)
                ->update([
                    "is_audit" => 1,
                    "pay_status" => 20,
                    "pay_time" => time()
                ]);
            // 发放积分
            $pointsCapitalModel = new PointsCaptial();
            $pointsCapitalModel->insert([
                "user_id" => $orderInfo["user_id"],
                "type" => 10, // 报单给积分两倍
                "order_id" => $orderInfo['order_id'],
                "points" => bcmul($orderInfo['order_price'], 2, 2),
                "description" => "会员商城赠送积分，等待释放",
                "create_time" => time(),
                "consignment_money" => 0.00
            ]);
            $userModel = new \app\api\model\User();
            $userModel->where("user_id", $orderInfo["user_id"])
                ->setInc("points", bcmul($orderInfo['order_price'], 2, 2));
            $userModel->where("user_id", $orderInfo["user_id"])
                ->setInc("all_points", bcmul($orderInfo['order_price'], 2, 2));
            $orderCompleteModel = new PaySuccess($orderInfo["order_no"]);
            $data = [
                "trade_no" => "10001",
                "out_trade_no" => $orderInfo["order_no"]
            ];
            $allUser = $userModel->select();
            foreach ($allUser as $user) {
                $userModel = new User();
                $userInfo = $userModel->where("user_id", $user["user_id"])->find();
                $level = $this->checkTeamGrade($user["user_id"]);
                if($userInfo["is_hand"] == 1) {
                    if($level > $userInfo["level"]){
                        $userModel->where("user_id", $user["user_id"])
                            ->update([
                                "level" => $level
                            ]);
                    }
                } else {
                    $userModel->where("user_id", $user["user_id"])
                        ->update([
                            "level" => $level
                        ]);
                }

            }
            // 开始赠送黄金券
            $gold_g = $orderInfo["order_price"] / 1000;
            $gold_g = bcadd($gold_g, 0 ,0);
            $goldCouponModel = new GoldCoupon();
            $goldCouponModel->save([
                "user_id" => $orderInfo["user_id"],
                "order_id" => $orderInfo["order_id"],
                "money" => $gold_g,
                "is_need_fee" => 0
            ]);
            $orderCompleteModel->onPaySuccess(PayTypeEnum::WECHAT, $data);
            $model->commit();
            return $this->renderSuccess("操作成功");
        } catch (BaseException $exception){
            return $this->renderError($exception->getMessage());
        }
    }

    public function checkTeamFirstGrade($user_id){
        $model = new \app\common\model\Order();
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
            ->where("pay_status", 20)
            ->sum("pay_price");
        if($totalPerformance >= 200000) {
            return true;
        }
        return false;
    }

    public function checkTeamSecondGrade($user_id){
        $model = new \app\api\model\Order();
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
            ->where("pay_status", 20)
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
        $model = new OrderModel();
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
            ->where("pay_status", 20)
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
        $model = new OrderModel();
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
            ->where("pay_status", 20)
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
        $model = new OrderModel();
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
            ->where("pay_status", 20)
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
        return $level;
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

    public function updateTeam(){
        $userModel = new User();
        $data = $userModel
            ->where("user_id", 10004)
            ->select();
        foreach ($data as $value){
            $level = $this->checkTeamGrade($value["user_id"]);
            if($value["is_hand"] == 1) {
                if($level > $value["level"]){
                    echo $value["user_id"] . "更新等级" . $level . "<br/>";
//                    $userModel->where("user_id", $user["user_id"])
//                        ->update([
//                            "level" => $level
//                        ]);
                } else {

                }
            } else {
                echo $value["user_id"] . "更新等级" . $level."<br/>";
//                $userModel->where("user_id", $user["user_id"])
//                    ->update([
//                        "level" => $level
//                    ]);
            }
        }
    }

    public function delete($order_id) {
        $model = new OrderModel();
        $model->where("order_id", $order_id)
            ->update([
                "is_delete" => 1
            ]);
        return $this->renderSuccess("删除成功");
    }
}
