<?php

namespace app\store\controller;

use app\api\service\order\PaySuccess;
use app\common\enum\order\PayType as PayTypeEnum;
use app\common\exception\BaseException;
use app\common\model\GoldCoupon;
use app\common\model\PointsCaptial;
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
            $orderCompleteModel = new PaySuccess($orderInfo["order_no"]);
            $data = [
                "trade_no" => "10001",
                "out_trade_no" => $orderInfo["order_no"]
            ];
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

}
