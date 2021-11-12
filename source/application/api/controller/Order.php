<?php

namespace app\api\controller;

use app\api\model\Cart as CartModel;
use app\api\model\dealer\Withdraw;
use app\api\model\Order as OrderModel;
use app\api\service\order\Checkout as CheckoutModel;
use app\api\service\order\PaySuccess;
use app\api\validate\order\Checkout as CheckoutValidate;
use app\common\enum\order\PayType;
use app\common\exception\BaseException;
use app\common\library\alipay\AlipayUser as AliPayUserModel;
use app\common\model\PointsCaptial;

vendor('aop.request.AlipayTradeWapPayRequest');
vendor('aop.request.AlipayTradePagePayRequest');
vendor('aop.request.AlipayTradeAppPayRequest');

/**
 * 订单控制器
 * Class Order
 * @package app\api\controller
 */
class Order extends Controller
{
    /* @var \app\api\model\User $user */
    private $user;

    /* @var CheckoutValidate $validate */
    private $validate;

    /**
     * 构造方法
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function _initialize()
    {
        parent::_initialize();
        // 用户信息
        $this->user = $this->getUser();
        // 验证类
        $this->validate = new CheckoutValidate;
    }

    /**
     * 订单确认-立即购买
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     * @throws \Exception
     */
    public function buyNow()
    {
        // 实例化结算台服务
        $Checkout = new CheckoutModel;
        // 订单结算api参数
        $params = $Checkout->setParam($this->getParam([
            'goods_id' => 0,
            'goods_num' => 0,
            'goods_sku_id' => '',
        ]));
        // 表单验证
        if (!$this->validate->scene('buyNow')->check($params)) {
            return $this->renderError($this->validate->getError());
        }
        // 立即购买：获取订单商品列表
        $model = new OrderModel;
        $goodsList = $model->getOrderGoodsListByNow(
            $params['goods_id'],
            $params['goods_sku_id'],
            $params['goods_num']
        );
        // 获取订单确认信息
        $orderInfo = $Checkout->onCheckout($this->user, $goodsList);
        if ($this->request->isGet()) {
            return $this->renderSuccess($orderInfo);
        }
        // 订单结算提交
        if ($Checkout->hasError()) {
            return $this->renderError($Checkout->getError());
        }
        // 创建订单
        if (!$Checkout->createOrder($orderInfo)) {
            return $this->renderError($Checkout->getError() ?: '订单创建失败');
        }
        $payment = $model->onOrderPayment($this->user, $Checkout->model, $params['pay_type']);
        // 返回结算信息
        return $this->renderSuccess([
            'order_id' => $Checkout->model['order_id'],   // 订单id
            'pay_type' => $params['pay_type'],  // 支付方式
            'payment' => $payment               // 微信支付参数
        ], ['success' => '支付成功', 'error' => '订单未支付']);
    }

    public function buyNowAlipay()
    {
        // 实例化结算台服务
        $Checkout = new CheckoutModel;
        // 订单结算api参数
        $params = $Checkout->setParam($this->getParam([
            'goods_id' => 0,
            'goods_num' => 0,
            'goods_sku_id' => '',
        ]));
        // 表单验证
        if (!$this->validate->scene('buyNow')->check($params)) {
            return $this->renderError($this->validate->getError(), null);
        }
        // 立即购买：获取订单商品列表
        $model = new OrderModel;
        $goodsList = $model->getOrderGoodsListByNow(
            $params['goods_id'],
            $params['goods_sku_id'],
            $params['goods_num']
        );
        // 获取订单确认信息
        $orderInfo = $Checkout->onCheckout($this->user, $goodsList);
        if ($this->request->isGet()) {
            return $this->renderSuccess($orderInfo, null);
        }
        // 订单结算提交
        if ($Checkout->hasError()) {
            return $this->renderError($Checkout->getError(), null);
        }
        // 创建订单
        if (!$Checkout->createOrder($orderInfo)) {
            return $this->renderError($Checkout->getError() ?: '订单创建失败', null);
        }
        // 构建支付宝请求
        $model = new OrderModel();
        $order_no = $model->where('order_id',$Checkout->model['order_id'])->field('order_no')->find()['order_no'];
        $orderInfo = $model->getPayDetail($order_no);
        if($goodsList[0]['category_id'] == 10006){
            // 检测当前订单积分是否满足
            $model = new \app\api\model\User();
            $needPoints = $goodsList[0]['total_points'];
            $userInfo = $this->getUser();
            if($userInfo['mall_points'] >= $needPoints){
                // 积分足够，进行扣积分操作
                $pointsCaptialModel = new PointsCaptial();
                try {
                    $model->where("user_id", $userInfo['user_id'])->setDec("mall_points", $needPoints);
                    // 记录积分流向
                    $pointsCaptialModel->insert([
                        "user_id" => $userInfo['user_id'],
                        "type" => 20,
                        "order_id" => $orderInfo['order_id'],
                        "points" => $needPoints,
                        "create_time" => time(),
                        "consignment_money" => 0.00
                    ]);
                    // 标注订单支付
                    $paySuccessModel = new PaySuccess($order_no);
                    $paySuccessModel->onPaySuccess(PayType::POINTS, [
                        "transaction_id" => "",
                        "trade_no" => ""
                    ]);
                    (new OrderModel())->where("order_id", $orderInfo['order_id'])->update([
                        "pay_type" => 40
                    ]);
                    $model->commit();
                    return $this->renderJson(2,"购买成功");
                } catch (\Exception $exception){
                    return $this->renderError($exception->getMessage());
                }
            } else {
                return $this->renderError("积分不足","");
            }
        } else {
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
            $request = new \AlipayTradeWapPayRequest();
            $info = json_encode(
                [
                    'body'=>$orderInfo['goods'][0]['goods_name'],
                    'subject'=>$orderInfo['goods'][0]['goods_name'],
                    'out_trade_no'=>$orderInfo['order_no'],
                    'timeout_express'=>'30m',
                    'total_amount'=>$orderInfo['pay_price'],
                    'product_code'=>'QUICK_WAP_WAY'],
                JSON_UNESCAPED_UNICODE);
            $request->setNotifyUrl($this->request->domain()."/alipay.php");
            $request->setReturnUrl($this->request->domain()."/?s=/mobile/mine");
            $request->setBizContent($info);
            $response = $aliUser->aop->pageExecute($request);
            //htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
            return $this->renderSuccess(htmlspecialchars_decode($response));
        }
    }

    public function buyNowAlipayForAndroid()
    {
        // 实例化结算台服务
        $Checkout = new CheckoutModel;
        // 订单结算api参数
        $params = $Checkout->setParam($this->getParam([
            'goods_id' => 0,
            'goods_num' => 0,
            'goods_sku_id' => '',
        ]));
        // 表单验证
        if (!$this->validate->scene('buyNow')->check($params)) {
            return $this->renderError($this->validate->getError(), null);
        }
        // 立即购买：获取订单商品列表
        $model = new OrderModel;
        $goodsList = $model->getOrderGoodsListByNow(
            $params['goods_id'],
            $params['goods_sku_id'],
            $params['goods_num']
        );
        // 获取订单确认信息
        $orderInfo = $Checkout->onCheckout($this->user, $goodsList);
        if ($this->request->isGet()) {
            return $this->renderSuccess($orderInfo, null);
        }
        // 订单结算提交
        if ($Checkout->hasError()) {
            return $this->renderError($Checkout->getError(), null);
        }
        // 创建订单
        if (!$Checkout->createOrder($orderInfo)) {
            return $this->renderError($Checkout->getError() ?: '订单创建失败', null);
        }
        // 构建支付宝请求
        $model = new OrderModel();
        $order_no = $model->where('order_id',$Checkout->model['order_id'])->field('order_no')->find()['order_no'];
        $orderInfo = $model->getPayDetail($order_no);
        if($goodsList[0]['category_id'] == "10006" || $goodsList[0]['category_id'] == "10002"){
            // 检测当前订单积分是否满足
            $model = new \app\api\model\User();
            $userInfo = $this->getUser();
            if($userInfo['mall_points'] >= $orderInfo['pay_price']){
                // 积分足够，进行扣积分操作
                $pointsCaptialModel = new PointsCaptial();
                try {
                    $model->where("user_id", $userInfo['user_id'])->setDec("mall_points", $orderInfo['pay_price']);
                    // 记录积分流向
                    $pointsCaptialModel->insert([
                        "user_id" => $userInfo['user_id'],
                        "type" => 50,
                        "order_id" => $orderInfo['order_id'],
                        "points" => $orderInfo['pay_price'],
                        "create_time" => time(),
                        "consignment_money" => 0.00
                    ]);
                    // 标注订单支付
                    $paySuccessModel = new PaySuccess($order_no);
                    $paySuccessModel->onPaySuccess(PayType::POINTS, [
                        "transaction_id" => "",
                        "trade_no" => ""
                    ]);
                    (new OrderModel())->where("order_id", $orderInfo['order_id'])->update([
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
        } else if($goodsList[0]['category_id'] == 10001) {
            // 上传截图
            $model->where("order_id", $orderInfo["order_id"])
                ->update([
                    "audit_image_id" => $params["audit_image_id"]
                ]);
            return $this->renderJson(3, "购买成功，请耐心等待积分到账。", "");
        } else {
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
//            $request = new \AlipayTradeWapPayRequest();
            $request = new \AlipayTradeAppPayRequest();
            $info = json_encode(
                [
                    'body' => $orderInfo['goods'][0]['goods_name'],
                    'subject' => $orderInfo['goods'][0]['goods_name'],
                    'out_trade_no'=>$orderInfo['order_no'],
                    'timeout_express'=>'180m',
//                    'total_amount'=>$orderInfo['pay_price'],
                    'total_amount'=>"0.01",
                    'product_code'=>'QUICK_MSECURITY_PAY'],
                JSON_UNESCAPED_UNICODE);

            $request->setNotifyUrl($this->request->domain()."/alipay.php");
            $request->setBizContent($info);
            $response = $aliUser->aop->sdkExecute($request);
            //htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
            return $this->renderSuccess(htmlspecialchars_decode($response));
        }
    }

    public function getTotalData(){
        $orderModel = new \app\api\model\Order();
        $dealerOrderModel = new \app\api\model\dealer\Order();
        $withdrawModel = new Withdraw();
        $totalPrice = $orderModel->sum("order_price");
        $payable = $dealerOrderModel->where('is_settled',1)->select();
        $payableMoney = 0.00;
        foreach ($payable as $key => $value){
            $payableMoney += $value['first_money'];
            $payableMoney += $value['second_money'];
            $payableMoney += $value['third_money'];
            if($value['team_money_resource'] !== ""){
                $team_money = 0.00;
                $team_money_resource = json_decode($value['team_money_resource'],true);
                foreach ($team_money_resource as $k => $v){
                    $team_money += $v['money'];
                }
                $payableMoney += $team_money;
            }
        }
        $totalWithdrawPrice = $withdrawModel->where('apply_status',40)->sum('money');

        $todayPrice = $orderModel->whereTime('create_time','today')->sum('order_price');
        $todayPayable = $dealerOrderModel->where('is_settled',1)->whereTime('create_time','today')->select();
        $todayPayablePrice = 0.00;
        foreach ($todayPayable as $key => $value){
            $todayPayablePrice += $value['first_money'];
            $todayPayablePrice += $value['second_money'];
            $todayPayablePrice += $value['third_money'];
            if($value['team_money_resource'] !== ""){
                $team_money = 0.00;
                $team_money_resource = json_decode($value['team_money_resource'],true);
                foreach ($team_money_resource as $k => $v){
                    $team_money += $v['money'];
                }
                $todayPayablePrice += $team_money;
            }
        }
        $todayWithdrawPrice = $withdrawModel->whereTime('create_time','today')->where('apply_status',40)->sum('money');
        $data = [
            'all' => [
                'total_money' => $totalPrice,
                'payable' => $payableMoney,
                'pay_after' => $totalWithdrawPrice,
                'precipitation' => bcsub($payableMoney,$totalWithdrawPrice,2)
            ],
            'today' => [
                'totay_money' => $todayPrice,
                'pay_able' => $todayPayablePrice,
                'pay_after' => $todayWithdrawPrice,
                'precipitation' => bcsub($todayPayablePrice,$todayWithdrawPrice,2)
            ]
        ];
        return $data;
    }

    /**
     * 订单确认-购物车结算
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \Exception
     */
    public function cart()
    {
        // 实例化结算台服务
        $Checkout = new CheckoutModel;
        // 订单结算api参数
        $params = $Checkout->setParam($this->getParam([
            'cart_ids' => '',
        ]));
        // 商品结算信息
        $CartModel = new CartModel($this->user);
        // 购物车商品列表
        $goodsList = $CartModel->getList($params['cart_ids']);
        // 获取订单结算信息
        $orderInfo = $Checkout->onCheckout($this->user, $goodsList);
        if ($this->request->isGet()) {
            return $this->renderSuccess($orderInfo);
        }
        // 创建订单
        if (!$Checkout->createOrder($orderInfo)) {
            return $this->renderError($Checkout->getError() ?: '订单创建失败');
        }
        // 移出购物车中已下单的商品
        $CartModel->clearAll($params['cart_ids']);
        // 构建微信支付请求
        $payment = $Checkout->onOrderPayment();
        // 返回状态
        return $this->renderSuccess([
            'order_id' => $Checkout->model['order_id'],   // 订单id
            'pay_type' => $params['pay_type'],  // 支付方式
            'payment' => $payment               // 微信支付参数
        ], ['success' => '支付成功', 'error' => '订单未支付']);
    }

    public function cartForAndroid()
    {
        // 实例化结算台服务
        $Checkout = new CheckoutModel;
        // 订单结算api参数
        $params = $Checkout->setParam($this->getParam([
            'cart_ids' => '',
        ]));
        // 商品结算信息
        $CartModel = new CartModel($this->user);
        // 购物车商品列表
        $goodsList = $CartModel->getList($params['cart_ids']);
        // 获取订单结算信息
        $orderInfo = $Checkout->onCheckout($this->user, $goodsList);
        if ($this->request->isGet()) {
            return $this->renderSuccess($orderInfo);
        }
        // 创建订单
        if (!$Checkout->createOrder($orderInfo)) {
            return $this->renderError($Checkout->getError() ?: '订单创建失败');
        }
        // 移出购物车中已下单的商品
        $CartModel->clearAll($params['cart_ids']);
        // 构建微信支付请求
        // 构建支付宝请求
        $model = new OrderModel();
        $order_no = $model->where('order_id',$Checkout->model['order_id'])->field('order_no')->find()['order_no'];
        $orderInfo = $model->getPayDetail($order_no);
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
                'body' => $orderInfo['goods'][0]['goods_name'],
                'subject' => $orderInfo['goods'][0]['goods_name'],
                'out_trade_no'=>$orderInfo['order_no'],
                'timeout_express'=>'180m',
                'total_amount'=>$orderInfo['pay_price'],
                'product_code'=>'QUICK_MSECURITY_PAY'],
            JSON_UNESCAPED_UNICODE);

        $request->setNotifyUrl($this->request->domain()."/alipay.php");
        $request->setBizContent($info);
        $response = $aliUser->aop->sdkExecute($request);
        //htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
        return $this->renderSuccess(htmlspecialchars_decode($response));
    }

    /**
     * 订单结算提交的参数
     * @param array $define
     * @return array
     */
    private function getParam($define = [])
    {
        return array_merge($define, $this->request->param());
    }

}
