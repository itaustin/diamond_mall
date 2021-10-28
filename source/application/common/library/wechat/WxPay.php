<?php

namespace app\common\library\wechat;

use app\api\model\Goods;
use app\api\model\Order as OrderModel;
use app\api\model\OrderGoods;
use app\common\library\alipay\AlipayUser as AliPayUserModel;
use app\common\model\AgentBonusRecord;
use app\common\model\AgentWith;
use app\common\model\AlipayTradeService;
use app\common\model\OrderAddress;
use app\common\model\Setting;
use app\common\model\store\AgentApply;
use app\common\model\store\User;
use app\common\model\Wxapp as WxappModel;
use app\common\enum\OrderType as OrderTypeEnum;
use app\common\enum\order\PayType as PayTypeEnum;
use app\common\exception\BaseException;
use app\common\service\order\Complete as OrderCompleteService;
use think\Queue;

vendor('aop.AopClient');
vendor('aop.request.AlipayTradeWapPayRequest');
vendor('aop.requrest.AlipayTradeService');
vendor('aop.requrest.AlipayTradePagePayRequest');

/**
 * 微信支付
 * Class WxPay
 * @package app\common\library\wechat
 */
class WxPay extends WxBase
{
    // 微信支付配置
    private $config;

    // 订单模型
    private $modelClass = [
        OrderTypeEnum::MASTER => 'app\api\service\order\PaySuccess',
        OrderTypeEnum::SHARING => 'app\api\service\sharing\order\PaySuccess',
        OrderTypeEnum::RECHARGE => 'app\api\service\recharge\PaySuccess',
    ];

    /**
     * 构造函数
     * WxPay constructor.
     * @param $config
     */
    public function __construct($config = false)
    {
        parent::__construct();
        $this->config = $config;
        $this->config !== false && $this->setConfig($this->config['app_id'], $this->config['app_secret']);
    }

    /**
     * 统一下单API
     * @param $order_no
     * @param $openid
     * @param $totalFee
     * @param int $orderType 订单类型
     * @return array
     * @throws BaseException
     */
    public function unifiedorder($order_no, $openid, $totalFee, $orderType = OrderTypeEnum::MASTER)
    {
        // 当前时间
        $time = time();
        // 生成随机字符串
        $nonceStr = md5($time . $openid);
        // API参数
        $params = [
            'appid' => $this->appId,
            'attach' => json_encode(['order_type' => $orderType]),
            'body' => $order_no,
            'mch_id' => $this->config['mchid'],
            'nonce_str' => $nonceStr,
            'notify_url' => base_url() . 'notice.php',  // 异步通知地址
            'openid' => $openid,
            'out_trade_no' => $order_no,
            'spbill_create_ip' => \request()->ip(),
            'total_fee' => $totalFee * 100, // 价格:单位分
            'trade_type' => 'JSAPI',
        ];
        // 生成签名
        $params['sign'] = $this->makeSign($params);
        // 请求API
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $result = $this->post($url, $this->toXml($params));
        $prepay = $this->fromXml($result);
        // 请求失败
        if ($prepay['return_code'] === 'FAIL') {
            throw new BaseException(['msg' => "微信支付api：{$prepay['return_msg']}", 'code' => -10]);
        }
        if ($prepay['result_code'] === 'FAIL') {
            throw new BaseException(['msg' => "微信支付api：{$prepay['err_code_des']}", 'code' => -10]);
        }
        // 生成 nonce_str 供前端使用
        $paySign = $this->makePaySign($params['nonce_str'], $prepay['prepay_id'], $time);
        return [
            'prepay_id' => $prepay['prepay_id'],
            'nonceStr' => $nonceStr,
            'timeStamp' => (string)$time,
            'paySign' => $paySign
        ];
    }

    /**
     * 统一下单API
     * @param $order_no
     * @param $openid
     * @param $totalFee
     * @param int $orderType 订单类型
     * @return array
     * @throws BaseException
     */
    public function unifiedorder_admin($order_no, $openid, $totalFee, $orderType = OrderTypeEnum::MASTER)
    {
        // 当前时间
        $time = time();
        // 生成随机字符串
        $nonceStr = md5($time . $openid);
        // API参数
        $params = [
            'appid' => $this->appId,
            'attach' => json_encode(['order_type' => $orderType]),
            'body' => $order_no,
            'mch_id' => $this->config['mchid'],
            'nonce_str' => $nonceStr,
            'notify_url' => base_url() . 'store.php',  // 异步通知地址
            'openid' => $openid,
            'out_trade_no' => $order_no,
            'spbill_create_ip' => \request()->ip(),
            'total_fee' => $totalFee * 100, // 价格:单位分
            'trade_type' => 'JSAPI',
        ];
        // 生成签名
        $params['sign'] = $this->makeSign($params);
        // 请求API
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $result = $this->post($url, $this->toXml($params));
        $prepay = $this->fromXml($result);
        // 请求失败
        if ($prepay['return_code'] === 'FAIL') {
            throw new BaseException(['msg' => "微信支付api：{$prepay['return_msg']}", 'code' => -10]);
        }
        if ($prepay['result_code'] === 'FAIL') {
            throw new BaseException(['msg' => "微信支付api：{$prepay['err_code_des']}", 'code' => -10]);
        }
        // 生成 nonce_str 供前端使用
        $paySign = $this->makePaySign($params['nonce_str'], $prepay['prepay_id'], $time);
        return [
            'prepay_id' => $prepay['prepay_id'],
            'nonceStr' => $nonceStr,
            'timeStamp' => (string)$time,
            'paySign' => $paySign
        ];
    }

    public function notify(){
        if (!$xml = file_get_contents('php://input')) {
            $this->returnCode(false, 'Not found DATA');
        }
//        $xml = <<<EOF
//<xml><appid><![CDATA[wx38d44f6a1bc6904d]]></appid>
//<attach><![CDATA[{"order_type":10}]]></attach>
//<bank_type><![CDATA[CITIC_DEBIT]]></bank_type>
//<cash_fee><![CDATA[69800]]></cash_fee>
//<fee_type><![CDATA[CNY]]></fee_type>
//<is_subscribe><![CDATA[N]]></is_subscribe>
//<mch_id><![CDATA[1604435317]]></mch_id>
//<nonce_str><![CDATA[77991235a9356b2e3ccef6d25aa0618b]]></nonce_str>
//<openid><![CDATA[oU6OY53YEuCHcOjEfUkReBUj70N0]]></openid>
//<out_trade_no><![CDATA[2021011110254100]]></out_trade_no>
//<result_code><![CDATA[SUCCESS]]></result_code>
//<return_code><![CDATA[SUCCESS]]></return_code>
//<sign><![CDATA[007A47804A3B8382231FA41D04A1B640]]></sign>
//<time_end><![CDATA[20210111143847]]></time_end>
//<total_fee>69800</total_fee>
//<trade_type><![CDATA[JSAPI]]></trade_type>
//<transaction_id><![CDATA[4200000918202101114568035183]]></transaction_id>
//</xml>
//EOF;
        // 将服务器返回的XML数据转化为数组
        $data = $this->fromXml($xml);
        // 记录日志
        $this->doLogs($xml);
        $this->doLogs($data);
        // 实例化订单模型
        $model = $this->getOrderModel($data['out_trade_no'], $data['attach']);
        // 订单信息
        $order = $model->getOrderInfo();
        empty($order) && $this->returnCode(false, '订单不存在');
        // 公众号配置信息
        $wxConfig = WxappModel::getWxappCache($order['wxapp_id']);
        // 设置支付秘钥
        $this->config['apikey'] = $wxConfig['apikey'];
        // 保存微信服务器返回的签名sign
        $dataSign = $data['sign'];
        // sign不参与签名算法
        unset($data['sign']);
        // 生成签名
        $sign = $this->makeSign($data);
        // 判断签名是否正确 判断支付状态
        if (
            ($sign !== $dataSign)
            || ($data['return_code'] !== 'SUCCESS')
            || ($data['result_code'] !== 'SUCCESS')
        ) {
            $this->returnCode(false, '签名失败');
        }
        // 订单支付成功后写入大盘分红队列
//        $orderInfo = (new \app\api\model\Order())->where("order_id",$order['order_id'])->find();
//        $jobHandlerClassName = 'app\api\job\sync';
//        $jobQueueName = "orderBonus";
//        $jobData = ['ts' => time(), 'orderParam' => $orderInfo, 'a' => 1];
//        Queue::later(1, $jobHandlerClassName, $jobData, $jobQueueName);
        // 订单支付成功业务处理
        $status = $model->onPaySuccess(PayTypeEnum::WECHAT, $data);
        $orderModel = new OrderModel();
        $setting_data = Setting::getAll('10001')['agent']['values'];
        if($order['is_area_bonus'] === 0){
            $orderAddressModel = new OrderAddress();
            $orderAddressInfo = $orderAddressModel->where('order_id',$order['order_id'])->find();
            $agentWithModel = new AgentWith();
            $storeUserModel = new User();

            //省份代理查找，找到后分润
            $provinceFind = [
                "province_id" => $orderAddressInfo["province_id"],
            ];
            $provinceFind['pay_price'] = [
                'egt',$setting_data['province']['order_num'] * $setting_data["province"]['order_price']
            ];
            /**
             * 查找市代理
             */
            $cityFind = [
                "province_id" => $orderAddressInfo["province_id"],
                "city_id" => $orderAddressInfo["city_id"]
            ];
            $cityFind['pay_price'] = [
                'egt',$setting_data['city']['order_num'] * $setting_data["city"]['order_price']
            ];
            /**
             * 查找小区代理
             */
            $regionFind = [
                "province_id" => $orderAddressInfo["province_id"],
                "city_id" => $orderAddressInfo["city_id"],
                "region_id" => $orderAddressInfo["region_id"]
            ];
            $regionFind['pay_price'] = [
                'egt',$setting_data['region']['order_num'] * $setting_data["region"]['order_price']
            ];
            $provinceInfo = $agentWithModel
                ->alias("agent_with")
                ->where($provinceFind)
                ->where("type",2)
                ->where("agent_apply.apply_status",20)
                ->where("agent_with.is_invalidation",0)
                ->join("agent_apply","agent_apply.agent_with_id = agent_with.agent_with_id")
                ->field("agent_with.*,agent_apply.*")
                ->find();
            $cityInfo = $agentWithModel
                ->alias("agent_with")
                ->where($cityFind)
                ->where("type",3)
                ->where("agent_apply.apply_status",20)
                ->where("agent_with.is_invalidation",0)
                ->join("agent_apply","agent_apply.agent_with_id = agent_with.agent_with_id")
                ->field("agent_with.*,agent_apply.*")
                ->find();
            $regionInfo = $agentWithModel
                ->alias("agent_with")
                ->where($regionFind)
                ->where("type",4)
                ->where("agent_apply.apply_status",20)
                ->where("agent_with.is_invalidation",0)
                ->join("agent_apply","agent_apply.agent_with_id = agent_with.agent_with_id")
                ->field("agent_with.*,agent_apply.*")
                ->find();
            $agent_record = [];
            if($provinceInfo){
                //开始给省代理分润
                if($storeUserModel->where("store_user_id",$provinceInfo['user_id'])->setInc("money",$setting_data["province"]["bonus"])){
                    $this->doLogs("省级代理分润".$setting_data["province"]["bonus"]);
                    $record = [
                        "user_id" => $provinceInfo["user_id"],
                        "bonus_money" => $setting_data["province"]["bonus"],
                        "order_id" => $order['order_id'],
                        "agent_type" => 2,
                        "create_time" => time(),
                        "update_time" => time()
                    ];
                    array_push($agent_record,$record);
                }
            }
            if($cityInfo){
                if($storeUserModel->where("store_user_id",$cityInfo['user_id'])->setInc("money",$setting_data["city"]["bonus"])){
                    $this->doLogs("市级代理分润".$setting_data["city"]["bonus"]);
                    $record = [
                        "user_id" => $cityInfo["user_id"],
                        "bonus_money" => $setting_data["city"]["bonus"],
                        "order_id" => $order['order_id'],
                        "agent_type" => 3,
                        "create_time" => time(),
                        "update_time" => time()
                    ];
                    array_push($agent_record,$record);
                }
            }
            if($regionInfo){
                if($storeUserModel->where("store_user_id",$regionInfo['user_id'])->setInc("money",$setting_data["region"]["bonus"])){
                    $this->doLogs("区级代理分润".$setting_data["region"]["bonus"]);
                    $record = [
                        "user_id" => $regionInfo["user_id"],
                        "bonus_money" => $setting_data["region"]["bonus"],
                        "order_id" => $order['order_id'],
                        "agent_type" => 4,
                        "create_time" => time(),
                        "update_time" => time()
                    ];
                    array_push($agent_record,$record);
                }
            }
            $address_detail = $orderAddressInfo['detail'];
            $area_agent = $agentWithModel->where([
                "province_id" => $orderAddressInfo['province_id'],
                "city_id" => $orderAddressInfo['city_id'],
                "region_id" => $orderAddressInfo['region_id'],
                "type" => 5,
                "is_invalidation" => 0,
                "pay_price" => ['>=' ,
                    $setting_data['area']['order_num'] * $setting_data["area"]['order_price']
                ]
            ])->select();
            $find_area_agent = [
                "store_user_id" => 0
            ];
            foreach ($area_agent as $key => $value){
                $area_id = mb_substr($value["area_id"],0 , 4);
                $is_have = count(explode($area_id, $address_detail));
                if($is_have > 1){
                    $find_area_agent['store_user_id'] = $value['user_id'];
                    break;
                }
            }
            if($find_area_agent['store_user_id'] !== 0){
                if($storeUserModel->where("store_user_id",$find_area_agent['store_user_id'])->setInc("money",$setting_data["area"]["bonus"])){
                    $this->doLogs("小区代理分润".$setting_data["area"]["bonus"]);
                    $record = [
                        "user_id" => $find_area_agent["store_user_id"],
                        "bonus_money" => $setting_data["area"]["bonus"],
                        "order_id" => $order['order_id'],
                        "agent_type" => 5,
                        "create_time" => time(),
                        "update_time" => time()
                    ];
                    array_push($agent_record,$record);
                }
            }
            $bonus_record_model = new AgentBonusRecord();
            $bonus_record_model->saveAll($agent_record);
            if(!empty($agent_record)){
                $orderModel->where("order_id",$order['order_id'])->update([
                    'is_area_bonus' => 1
                ]);
            }
        }
        $orderModel = $orderModel::getUserOrderDetail($order['order_id'],$order['user_id']);
        $orderGoodsModel = new OrderGoods();
        $goods_id = $orderGoodsModel->where('order_id',$order['order_id'])->field('goods_id')->find()['goods_id'];
        $mall_no = (new Goods())->where('goods_id',$goods_id)->field('mall_no')->find()['mall_no'];
        if($mall_no == 1){
            $OrderCompleteService = new OrderCompleteService(OrderTypeEnum::MASTER);
            $OrderCompleteService->complete([$orderModel], "10001");
        }
        if ($status == false) {
            $this->returnCode(false, $model->getError());
        }
        // 返回状态
        $this->returnCode(true, 'OK');
    }

    /**
     * 支付成功异步通知
     * @throws BaseException
     * @throws \Exception
     * @throws \think\exception\DbException
     */
    public function notify_alipay($data)
    {
        $data['fund_bill_list'] = htmlspecialchars_decode($data['fund_bill_list']);
        $str = "";
        foreach ($data as $key => $value){
            $str .= $key . '=' .$value . '&';
        }
        $str = substr($str,0,strlen($str)-1);
        $this->doLogs($str);
        $this->doLogs($data);
        $aopModel = new AliPayUserModel(
            config('alipay')['appId'],
            config('alipay')['gatewayUrl'],
            config('alipay')['rsaPrivateKey'],
            config('alipay')['alipayRsaPublicKey'],
            '',
            'RSA2',
            'UTF-8',
            'json'
        );
        $flag = $aopModel->aop->rsaCheckV1($data,null,'RSA2');
        if($flag){
            $this->doLogs("验签成功");
            $this->doLogs("开始处理订单结算");
            // 实例化订单模型
            $model = $this->getOrderModel($data['out_trade_no'], json_encode(['order_type' => 10]));
            // 订单信息
            $order = $model->getOrderInfo();
            empty($order) && $this->returnCode(false, '订单不存在');
            if(!empty($order)){
                // 订单支付成功业务处理
                $status = $model->onPaySuccess(PayTypeEnum::WECHAT, $data);
                $orderModel = new OrderModel();
                $setting_data = Setting::getAll('10001')['agent']['values'];
                $orderModel = $orderModel::getUserOrderDetail($order['order_id'],$order['user_id']);
                $orderGoodsModel = new OrderGoods();
//                $goods_id = $orderGoodsModel->where('order_id',$order['order_id'])->field('goods_id')->find()['goods_id'];
//                $mall_no = (new Goods())->where('goods_id',$goods_id)->field('mall_no')->find()['mall_no'];
//                if($mall_no == 1){
//                    $OrderCompleteService = new OrderCompleteService(OrderTypeEnum::MASTER);
//                    $OrderCompleteService->complete([$orderModel], "10001");
//                }
                if ($status == false) {
                    $this->returnCode(false, $model->getError());
                }
                // 返回状态
                $this->returnCode(true, 'OK');
            }else{
                $this->returnCode(false,'ERROR');
            }
            $this->doLogs('订单结算已完成');
        }else{
            $this->doLogs("非法调用：签名验证失败");
        }

    }

    public function apply_admin($data)
    {
        if (!$xml = file_get_contents('php://input')) {
            $this->returnCode(false, 'Not found DATA');
        }
//        $xml = <<<EOF
//<xml><appid><![CDATA[wx38d44f6a1bc6904d]]></appid>
//<attach><![CDATA[{"order_type":10}]]></attach>
//<bank_type><![CDATA[OTHERS]]></bank_type>
//<cash_fee><![CDATA[1]]></cash_fee>
//<fee_type><![CDATA[CNY]]></fee_type>
//<is_subscribe><![CDATA[Y]]></is_subscribe>
//<mch_id><![CDATA[1604435317]]></mch_id>
//<nonce_str><![CDATA[1ebbb662b572b88ac38dd9064c4a551a]]></nonce_str>
//<openid><![CDATA[oU6OY5wZOAnkgonjtoXYCEXDNHfM]]></openid>
//<out_trade_no><![CDATA[2020121010210052]]></out_trade_no>
//<result_code><![CDATA[SUCCESS]]></result_code>
//<return_code><![CDATA[SUCCESS]]></return_code>
//<sign><![CDATA[60DB3D38E5265B57CBBCD0A3DD83491E]]></sign>
//<time_end><![CDATA[20201210123604]]></time_end>
//<total_fee>1</total_fee>
//<trade_type><![CDATA[JSAPI]]></trade_type>
//<transaction_id><![CDATA[4200000824202012106478105395]]></transaction_id>
//</xml>
//EOF;
        // 将服务器返回的XML数据转化为数组
        $data = $this->fromXml($xml);
        // 记录日志
        $this->doLogs($xml);
        $this->doLogs($data);
        $this->doLogs("结算完成");
        // 实例化订单模型
        $model = new AgentWith();
        // 订单信息
        $order = $model->with(['agent'])->where("order_no",$data['out_trade_no'])->find();
        empty($order) && $this->returnCode(false, '订单不存在');
        $model->where("agent_with_id",$order['agent_with_id'])->update([
            'transaction_id' => $data['transaction_id']
        ]);
        if(!empty($order)){
            $this->doLogs((new AgentApply)->where("apply_id",$order['agent']['apply_id'])->update([
                'pay_status' => 20
            ]));
            // 返回状态
            $this->returnCode(true, 'OK');
        }else{
            $this->returnCode(false,'ERROR');
        }
        $this->doLogs('订单结算已完成');

    }

    public function apply_notify($data)
    {
        $data['fund_bill_list'] = htmlspecialchars_decode($data['fund_bill_list']);
        $str = "";
        foreach ($data as $key => $value){
            $str .= $key . '=' .$value . '&';
        }
        $str = substr($str,0,strlen($str)-1);
        $this->doLogs($str);
        $this->doLogs($data);
        $aopModel = new AliPayUserModel(
            config('alipay')['appId'],
            config('alipay')['gatewayUrl'],
            config('alipay')['rsaPrivateKey'],
            config('alipay')['alipayRsaPublicKey'],
            '',
            'RSA2',
            'UTF-8',
            'json'
        );
        $flag = $aopModel->aop->rsaCheckV1($data,null,'RSA2');
        if($flag){
            $this->doLogs("验签成功");
            $this->doLogs("开始处理订单结算");
            // 实例化订单模型
            $model = new AgentWith();
            // 订单信息
            $order = $model->with(['agent'])->where("order_no",$data['out_trade_no'])->find();
            $this->doLogs($order->toArray());
            dump($order);
            empty($order) && $this->returnCode(false, '订单不存在');
            $model->where("agent_with_id",$order['agent_with_id'])->update([
                'transaction_id' => $data['trade_no']
            ]);
            if(!empty($order)){
                (new AgentApply)->where("apply_id",$order['agent']['apply_id'])->update([
                    'pay_status' => 20
                ]);
                // 返回状态
                $this->returnCode(true, 'OK');
            }else{
                $this->returnCode(false,'ERROR');
            }
            $this->doLogs('订单结算已完成');
        }else{
            $this->doLogs("非法调用：签名验证失败");
        }

    }

    /**
     * 申请退款API
     * @param $transaction_id
     * @param  double $total_fee 订单总金额
     * @param  double $refund_fee 退款金额
     * @return bool
     * @throws BaseException
     */
    public function refund($transaction_id, $total_fee, $refund_fee)
    {
        // 当前时间
        $time = time();
        // 生成随机字符串
        $nonceStr = md5($time . $transaction_id . $total_fee . $refund_fee);
        // API参数
        $params = [
            'appid' => $this->appId,
            'mch_id' => $this->config['mchid'],
            'nonce_str' => $nonceStr,
            'transaction_id' => $transaction_id,
            'out_refund_no' => $time,
            'total_fee' => $total_fee * 100,
            'refund_fee' => $refund_fee * 100,
        ];
        // 生成签名
        $params['sign'] = $this->makeSign($params);
        // 请求API
        $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
        $result = $this->post($url, $this->toXml($params), true, $this->getCertPem());
        // 请求失败
        if (empty($result)) {
            throw new BaseException(['msg' => '微信退款api请求失败']);
        }
        // 格式化返回结果
        $prepay = $this->fromXml($result);
        // 请求失败
        if ($prepay['return_code'] === 'FAIL') {
            throw new BaseException(['msg' => 'return_msg: ' . $prepay['return_msg']]);
        }
        if ($prepay['result_code'] === 'FAIL') {
            throw new BaseException(['msg' => 'err_code_des: ' . $prepay['err_code_des']]);
        }
        return true;
    }

    /**
     * 企业付款到零钱API
     * @param $order_no
     * @param $openid
     * @param $amount
     * @param $desc
     * @return bool
     * @throws BaseException
     */
    public function transfers($order_no, $openid, $amount, $desc)
    {
        // API参数
        $params = [
            'mch_appid' => $this->appId,
            'mchid' => $this->config['mchid'],
            'nonce_str' => md5(uniqid()),
            'partner_trade_no' => $order_no,
            'openid' => $openid,
            'check_name' => 'NO_CHECK',
            'amount' => $amount * 100,
            'desc' => $desc,
            'spbill_create_ip' => \request()->ip(),
        ];
        // 生成签名
        $params['sign'] = $this->makeSign($params);
        // 请求API
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
        $result = $this->post($url, $this->toXml($params), true, $this->getCertPem());
	// 请求失败
        if (empty($result)) {
            throw new BaseException(['msg' => '微信退款api请求失败']);
        }
        // 格式化返回结果
        $prepay = $this->fromXml($result);
        // 请求失败
        if ($prepay['return_code'] === 'FAIL') {
            throw new BaseException(['msg' => 'return_msg: ' . $prepay['return_msg']]);
        }
        if ($prepay['result_code'] === 'FAIL') {
            throw new BaseException(['msg' => 'err_code_des: ' . $prepay['err_code_des']]);
        }
        return true;
    }

    /**
     * 获取cert证书文件
     * @return array
     * @throws BaseException
     */
    private function getCertPem()
    {
        if (empty($this->config['cert_pem']) || empty($this->config['key_pem'])) {
            throw new BaseException(['msg' => '请先到后台公众号设置填写微信支付证书文件']);
        }
        // cert目录
        $filePath = __DIR__ . '/cert/' . $this->config['wxapp_id'] . '/';
        return [
            'certPem' => $filePath . 'cert.pem',
            'keyPem' => $filePath . 'key.pem'
        ];
    }

    /**
     * 实例化订单模型 (根据attach判断)
     * @param $orderNo
     * @param null $attach
     * @return mixed
     */
    private function getOrderModel($orderNo, $attach = null)
    {
        $attach = json_decode($attach, true);
        // 判断订单类型返回对应的订单模型
        $model = $this->modelClass[$attach['order_type']];
        return new $model($orderNo);
    }

    /**
     * 返回状态给微信服务器
     * @param boolean $returnCode
     * @param string $msg
     */
    private function returnCode($returnCode = true, $msg = null)
    {
        // 返回状态
        $return = [
            'return_code' => $returnCode ? 'SUCCESS' : 'FAIL',
            'return_msg' => $msg ?: 'OK',
        ];
        // 记录日志
        log_write([
            'describe' => '返回微信支付状态',
            'data' => $return
        ]);
        die($this->toXml($return));
    }

    /**
     * 生成paySign
     * @param $nonceStr
     * @param $prepay_id
     * @param $timeStamp
     * @return string
     */
    private function makePaySign($nonceStr, $prepay_id, $timeStamp)
    {
        $data = [
            'appId' => $this->appId,
            'nonceStr' => $nonceStr,
            'package' => 'prepay_id=' . $prepay_id,
            'signType' => 'MD5',
            'timeStamp' => $timeStamp,
        ];
        // 签名步骤一：按字典序排序参数
        ksort($data);
        $string = $this->toUrlParams($data);
        // 签名步骤二：在string后加入KEY
        $string = $string . '&key=' . $this->config['apikey'];
        // 签名步骤三：MD5加密
        $string = md5($string);
        // 签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 将xml转为array
     * @param $xml
     * @return mixed
     */
    private function fromXml($xml)
    {
        // 禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    /**
     * 生成签名
     * @param $values
     * @return string 本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
     */
    private function makeSign($values)
    {
        //签名步骤一：按字典序排序参数
        ksort($values);
        $string = $this->toUrlParams($values);
        //签名步骤二：在string后加入KEY
        $string = $string . '&key=' . $this->config['apikey'];
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 格式化参数格式化成url参数
     * @param $values
     * @return string
     */
    private function toUrlParams($values)
    {
        $buff = '';
        foreach ($values as $k => $v) {
            if ($k != 'sign' && $v != '' && !is_array($v)) {
                $buff .= $k . '=' . $v . '&';
            }
        }
        return trim($buff, '&');
    }

    /**
     * 输出xml字符
     * @param $values
     * @return bool|string
     */
    private function toXml($values)
    {
        if (!is_array($values)
            || count($values) <= 0
        ) {
            return false;
        }

        $xml = "<xml>";
        foreach ($values as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

}
