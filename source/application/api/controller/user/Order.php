<?php

namespace app\api\controller\user;

use app\api\controller\Controller;

use app\api\model\Goods;
use app\api\model\Order as OrderModel;
use app\api\model\Setting as SettingModel;
use app\api\service\order\PaySuccess;
use app\common\enum\order\PayType;
use app\common\enum\OrderType as OrderTypeEnum;
use app\common\enum\order\PayType as PayTypeEnum;
use app\common\library\alipay\AlipayUser as AliPayUserModel;
use app\common\model\PointsCaptial;
use app\common\service\qrcode\Extract as ExtractQRcode;

vendor('aop.request.AlipayTradeWapPayRequest');
vendor('aop.request.AlipayTradePagePayRequest');
vendor('aop.request.AlipayTradeAppPayRequest');

/**
 * 用户订单管理
 * Class Order
 * @package app\api\controller\user
 */
class Order extends Controller
{
    /* @var \app\api\model\User $user */
    private $user;

    /**
     * 构造方法
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->user = $this->getUser();   // 用户信息
    }

    /**
     * 我的订单列表
     * @param $dataType
     * @return array
     * @throws \think\exception\DbException
     */
    public function lists($dataType)
    {
        $model = new OrderModel;
        $list = $model->getList($this->user['user_id'], $dataType);
        return $this->renderSuccess(compact('list'));
    }

    /**
     * 我的订单列表
     * @param $dataType
     * @return array
     * @throws \think\exception\DbException
     */
    public function listsForAndroid($dataType)
    {
        $model = new OrderModel;
        $goodsModel = new Goods();
        $list = $model->getList($this->user['user_id'], $dataType);
        foreach ($list as &$value) {
            foreach ($value["goods"] as &$goodsValue) {
                $goodsValue["category_id"] = $goodsModel
                    ->where("goods_id", $goodsValue["goods_id"])
                    ->value("category_id");
            }
        }
        return [
            "code" => 1,
            "msg" => "成功",
            "orderList" => $list
        ];
    }

    /**
     * 订单详情信息
     * @param $order_id
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function detail($order_id)
    {
        // 订单详情
        $model = OrderModel::getUserOrderDetail($order_id, $this->user['user_id']);
        // 该订单是否允许申请售后
        $model['isAllowRefund'] = $model->isAllowRefund();
        return $this->renderSuccess([
            'order' => $model,  // 订单详情
            'setting' => [
                // 积分名称
                'points_name' => SettingModel::getPointsName(),
            ],
        ]);
    }

    /**
     * 获取物流信息
     * @param $order_id
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function express($order_id)
    {
        // 订单信息
        $order = OrderModel::getUserOrderDetail($order_id, $this->user['user_id']);
        if (!$order['express_no']) {
            return $this->renderError('没有物流信息');
        }
        // 获取物流信息
        /* @var \app\store\model\Express $model */
        $model = $order['express'];
        $express = $model->dynamic($model['express_name'], $model['express_code'], $order['express_no']);
        if ($express === false) {
            return $this->renderError($model->getError());
        }
        return $this->renderSuccess(compact('express'));
    }

    /**
     * 取消订单
     * @param $order_id
     * @return array
     * @throws \Exception
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function cancel($order_id)
    {
        $model = OrderModel::getUserOrderDetail($order_id, $this->user['user_id']);
        if ($model->cancel($this->user)) {
            return $this->renderSuccess("",$model->getError() ?: '订单取消成功');
        }
        return $this->renderError($model->getError() ?: '订单取消失败',"");
    }

    /**
     * 确认收货
     * @param $order_id
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function receipt($order_id)
    {
        $model = OrderModel::getUserOrderDetail($order_id, $this->user['user_id']);
        if ($model->receipt()) {
            return $this->renderSuccess("","收货成功");
        }
        return $this->renderError($model->getError(),"");
    }

    /**
     * 立即支付
     * @param int $order_id 订单id
     * @param int $payType 支付方式
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function pay($order_id, $payType = PayTypeEnum::WECHAT)
    {
        // 获取订单详情
        $model = OrderModel::getUserOrderDetail($order_id, $this->user['user_id']);
        // 订单支付事件
        if (!$model->onPay($payType)) {
            return $this->renderError($model->getError() ?: '订单支付失败');
        }
        // 构建微信支付请求
        $payment = $model->onOrderPayment($this->user, $model, $payType);
        // 支付状态提醒
        return $this->renderSuccess([
            'order_id' => $model['order_id'],   // 订单id
            'pay_type' => $payType,             // 支付方式
            'payment' => $payment               // 微信支付参数
        ], ['success' => '支付成功', 'error' => '订单未支付']);
    }

    public function payForAndroid($order_id, $payType = PayTypeEnum::WECHAT)
    {
        // 获取订单详情
        $model = OrderModel::getUserOrderDetail($order_id, $this->user['user_id']);
        // 订单支付事件
        if (!$model->onPay($payType)) {
            return $this->renderError($model->getError() ?: '订单支付失败');
        }
        $userModel = new \app\api\model\User();
        $userInfo = $this->getUser();
        if(count($model['goods']) <= 1 && $model['goods'][0]['goods']['category_id'] == 10006 || $model['goods'][0]['goods']['category_id'] == 10002){
            if($userInfo['points'] >= $model['pay_price']){
                // 积分足够，进行扣积分操作
                $pointsCaptialModel = new PointsCaptial();
                try {
                    $userModel->where("user_id", $userInfo['user_id'])->setDec("points", $model['pay_price']);
                    // 记录积分流向
                    $pointsCaptialModel->insert([
                        "user_id" => $userInfo['user_id'],
                        "type" => 50,
                        "order_id" => $model['order_id'],
                        "points" => $model['pay_price'],
                        "create_time" => time(),
                        "consignment_money" => 0.00
                    ]);
                    // 标注订单支付
                    $paySuccessModel = new PaySuccess($model["order_no"]);
                    $paySuccessModel->onPaySuccess(PayType::POINTS, [
                        "transaction_id" => "",
                        "trade_no" => ""
                    ]);
                    (new OrderModel())->where("order_id", $model['order_id'])->update([
                        "pay_type" => 40
                    ]);
                    $model->commit();
                    return $this->renderJson(2,"购买成功","");
                } catch (\Exception $exception){
                    return $this->renderError($exception->getMessage());
                }
            } else {
                return $this->renderError("积分不足","");
            }
        } else {
            // 构建支付请求
            $aliUser = new AliPayUserModel(
                config('alipay')['appId'],
                config('alipay')['gatewayUrl'],
                config('alipay')['rsaPrivateKey'],
                config('alipay')['alipayRsaPublicKey'],
                '',
                'RSA2',
                'UTF-8',
                'json'
            );
            $request = new \AlipayTradeAppPayRequest();
            $info = json_encode(
                [
                    'body'=>$model['goods'][0]['goods_name'],
                    'subject'=>$model['goods'][0]['goods_name'],
                    'out_trade_no'=>$model['order_no'],
                    'timeout_express'=>'180m',
                    'total_amount'=>$model['pay_price'],
                    'product_code'=>'QUICK_MSECURITY_PAY'],
                JSON_UNESCAPED_UNICODE);
            $request->setNotifyUrl($this->request->domain()."/alipay.php");
            $request->setBizContent($info);
            $response = $aliUser->aop->sdkExecute($request);
            return $this->renderSuccess($response);
        }
    }

    /**
     * 获取订单核销二维码
     * @param $order_id
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function extractQrcode($order_id)
    {
        // 订单详情
        $order = OrderModel::getUserOrderDetail($order_id, $this->user['user_id']);
        // 判断是否为待核销订单
        if (!$order->checkExtractOrder($order)) {
            return $this->renderError($order->getError());
        }
        $Qrcode = new ExtractQRcode(
            $this->wxapp_id,
            $this->user,
            $order_id,
            OrderTypeEnum::MASTER
        );
        return $this->renderSuccess([
            'qrcode' => $Qrcode->getImage(),
        ]);
    }

}
