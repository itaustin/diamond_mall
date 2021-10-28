<?php

namespace app\common\service\order;

use app\common\exception\BaseException;
use app\common\library\alipay\AlipayUser as AliPayUserModel;
use app\common\model\PointsCaptial;
use app\common\model\User as UserModel;
use app\common\model\Wxapp as WxappModel;
use app\common\model\user\BalanceLog as BalanceLogModel;
use app\common\enum\order\PayType as PayTypeEnum;
use app\common\enum\user\balanceLog\Scene as SceneEnum;
use app\common\library\wechat\WxPay;

vendor('aop.request.AlipayTradeRefundRequest');

/**
 * 订单退款服务类
 * Class Refund
 * @package app\common\service\order
 */
class Refund
{
    /**
     * 执行订单退款
     * @param \app\common\model\BaseModel $order 订单信息
     * @param double|null $money 指定退款金额
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\DbException
     * @throws \app\common\exception\BaseException
     */
    public function execute(&$order, $money = null)
    {
        // 退款金额，如不指定则默认为订单实付款金额
        is_null($money) && $money = $order['pay_price'];
        // 1.微信支付退款
        if ($order['pay_type']['value'] == PayTypeEnum::WECHAT) {
            return $this->wxpay($order, $money);
        }
        // 1.支付宝退款
        if ($order['pay_type']['value'] == PayTypeEnum::ALIPAY) {
            return $this->alipay($order, $money);
        }
        // 积分支付
        if ($order['pay_type']['value'] == PayTypeEnum::POINTS) {
            return $this->points($order, $money);
        }
        // 2.余额支付退款
        if ($order['pay_type']['value'] == PayTypeEnum::BALANCE) {
            return $this->balance($order, $money);
        }
        return false;
    }

    /**
     * 余额支付退款
     * @param $order
     * @param $money
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    private function balance(&$order, $money)
    {
        // 回退用户余额
        $user = UserModel::detail($order['user_id']);
        $user->setInc('balance', $money);
        // 记录余额明细
        BalanceLogModel::add(SceneEnum::REFUND, [
            'user_id' => $user['user_id'],
            'money' => $money,
        ], ['order_no' => $order['order_no']]);
        return true;
    }

    /**
     * 微信支付退款
     * @param $order
     * @param double $money
     * @return bool
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    private function wxpay(&$order, $money)
    {
        $wxConfig = WxappModel::getWxappCache($order['wxapp_id']);
        $WxPay = new WxPay($wxConfig);
        return $WxPay->refund($order['transaction_id'], $order['pay_price'], $money);
    }

    private function alipay(&$order, $money){
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
        $request = new \AlipayTradeRefundRequest();
        $info = json_encode(
            [
                'trade_no' => $order['transaction_id'],
                'refund_amount' => $money,
                'out_request_no' => createOrderNo()
            ],
            JSON_UNESCAPED_UNICODE);
        $request->setBizContent($info);
        $response = $aliUser->aop->execute($request);
        $response_data = object_array($response);
        if($response_data['alipay_trade_refund_response']['code'] == "10000"){
            return true;
        } else {
            return false;
        }
    }

    public function points(&$order, $money){
        $userModel = new UserModel();
        $userModel->startTrans();
        try {
            $pointsCaptialModel = new PointsCaptial();
            $pointsCaptialModel->insert([
                "user_id" => $order['user_id'],
                "type" => 60,
                "order_id" => $order['order_id'],
                "points" => $order['pay_price'],
                "create_time" => time(),
                "consignment_money" => 0.00
            ]);
            $userModel->where("user_id", $order['user_id'])->setInc("mall_points", $money);
            $userModel->commit();
            return true;
        } catch (BaseException $exception){
            return false;
        }
    }

}