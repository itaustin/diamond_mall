<?php

namespace app\store\controller;

use app\api\service\Payment;
use app\common\exception\BaseException;
use app\common\library\alipay\AlipayUser as AliPayUserModel;
use app\store\model\AgentWith;
use app\store\model\CustomArea;
use app\common\model\Region;
use app\store\model\store\AgentApply;
use app\store\model\store\User as StoreUserModel;
use think\Cache;
use think\Cookie;
use think\Db;
use think\Exception;
use think\Queue;
use think\Session;
use app\store\model\Setting as SettingModel;

vendor('aop.request.AlipayTradeWapPayRequest');
vendor('aop.requrest.AlipayTradePagePayRequest');

/**
 * 商户认证
 * Class Passport
 * @package app\store\controller
 */
class Passport extends Controller
{
    /**
     * 商户后台登录
     * @return array|bool|mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function login()
    {
        $userSession = Session::get('zuowey_store');
        if (!empty($userSession)) {
            if ($userSession['is_login'] == 1) {
                $this->redirect('index/index');
            }
        }
        if ($this->request->isAjax()) {
            $model = new StoreUserModel();
            if ($model->login($this->postData('User'))) {
                return $this->renderSuccess('登录成功', url('index/index'));
            }
            return $this->renderError($model->getError() ?: '登录失败');
        }
        $wechat_info = Cookie::get('user_info');
        if (empty($wechat_info) && $this->is_wechat()) {
            $this->redirect("https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx38d44f6a1bc6904d&redirect_uri=https://paimaimall.zuowey.com/?s=/mobile/wechat/getcode_store_register&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect");
        }
        $this->view->engine->layout(false);
        return $this->fetch('login', [
            // 系统版本号
            'version' => get_version()
        ]);
    }

    public function register()
    {
        if ($this->request->isAjax()) {
            $model = new StoreUserModel;
            $param = $this->postData('User');
            $data['user_name'] = $param['user_name'];
            $data['password'] = $param['password'];
            $data['password_confirm'] = $param['password_confirm'];
            $data['role_id'] = [3];
            $data['real_name'] = $param['real_name'];
            $data['openid'] = $param['openid'];
            if ($param['code'] == Cache::get('sms_' . $param['user_name'])) {
                $isExist = $model->where("user_name", $data['user_name'])->where("is_delete", 0)->field('store_user_id,user_name')->find();
                if (empty($isExist)) {
                    if ($real_name = $model->add($data)) {
                        return $this->renderSuccess("注册成功", "/?s=/store/passport/select&username=" . $real_name);
                    } else {
                        return $this->renderError($model->getError() ?: '添加失败');
                    }
                } else {
                    return $this->renderSuccess('继续注册', "/?s=/store/passport/select&username=" . $isExist["user_name"]);
                }
            } else {
                return $this->renderError("手机验证码错误");
            }
        }
        $wechat_info = Cookie::get('user_info');
        if (empty($wechat_info) && $this->is_wechat()) {
            $this->redirect("https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx38d44f6a1bc6904d&redirect_uri=https://paimaimall.zuowey.com/?s=/mobile/wechat/getcode_store_register&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect");
        }
        $this->assign('user_info', $wechat_info);
        $this->view->engine->layout(false);
        return $this->fetch('passport/register', [
            // 系统版本号
            'version' => get_version()
        ]);
    }

    public function select()
    {
        if ($this->request->isAjax()) {
            $identity = input('identity');
            $data["identity"] = $identity;
            $data['user_id'] = input('user_id');
            $setting_data = SettingModel::getAll('10001')['agent']['values'];
            if ($identity == 'province') {
                // 省份处理
                $data['type'] = '2';
                $data["order_num"] = $setting_data["province"]['order_num'];
                $data["order_price"] = $setting_data["province"]['order_price'];
                $data["order_task"] = $setting_data["province"]["task"];
            } else if ($identity == 'city') {
                // 城市处理
                $data['type'] = '3';
                $data["order_num"] = $setting_data["city"]['order_num'];
                $data["order_price"] = $setting_data["city"]['order_price'];
                $data["order_task"] = $setting_data["city"]["task"];
            } else if ($identity == 'region') {
                // 地区处理
                $data['type'] = '4';
                $data["order_num"] = $setting_data["region"]['order_num'];
                $data["order_price"] = $setting_data["region"]['order_price'];
                $data["order_task"] = $setting_data["region"]["task"];
            } else if ($identity == 'area') {
                // 小区处理
                $data['type'] = '5';
                $data["order_num"] = $setting_data["area"]['order_num'];
                $data["order_price"] = $setting_data["area"]['order_price'];
                $data["order_task"] = $setting_data["area"]["task"];
            }
            $data["province_id"] = input('province_id');
            $data["city_id"] = input('city_id');
            $data["region_id"] = input('region_id');
            $data["area_id"] = input('area_id');
            $newStr = preg_replace('/[^\x{4e00}-\x{9fa5}]/u', '', $data["area_id"]);
            if ($identity == 'area') {
                $countstr = mb_strlen($newStr, "utf-8");
                if ($countstr < 4) {
                    throw new BaseException([
                        'msg' => '小区名称最低4个汉字'
                    ]);
                }
            }
            $data["pay_price"] = input('pay_price');
            $model = new AgentWith();
            $userModel = new StoreUserModel();
            $orderModel = new \app\api\model\Order();
            if ($model->initSearch($data)) {
                return $this->renderError("该区域已被他人申请，请更换区域后再试。");
            }
            if (!$apply_id = $model->checkExist($data)) {
                if ($apply_id = $model->add($data)) {
                    if ($data["pay_price"] > 1000) {
                        return json([
                            "code" => 2,
                            "msg" => "显示收款码"
                        ]);
                    }
                    $userInfo = $userModel->where("store_user_id", input('user_id'))->find();
                    $user = [
                        "wxapp_id" => 10001,
                        "open_id" => $userInfo['openid'],
                        "user_id" => $userInfo['store_user_id']
                    ];
                    $applyData = (new AgentApply())->where('apply_id', $apply_id['apply_id'])->with('agent')->find();
                    $order = [
                        "order_id" => $applyData['apply_id'],
                        "order_no" => $applyData['agent']['order_no'],
                        "pay_price" => $applyData['agent']['pay_price']
                    ];
                    if ($this->is_wechat()) {
                        $payment = $orderModel->onOrderPayment_($user, $order, 20);
                        return [
                            "code" => 3,
                            "data" => [
                                'order_id' => $order['order_id'],   // 订单id
                                'pay_type' => 10,  // 支付方式
                                'payment' => $payment               // 微信支付参数
                            ],
                            "msg" => ['success' => '支付成功', 'error' => '订单未支付']
                        ];
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
                                'body' => '代理申请',
                                'subject' => '代理申请',
                                'out_trade_no' => $order['order_no'],
                                'timeout_express' => '30m',
                                'total_amount' => $order['pay_price'],
                                'product_code' => 'QUICK_WAP_WAY'
                            ], JSON_UNESCAPED_UNICODE);
                        $request->setNotifyUrl($this->request->domain() . "/apply.php");
                        $request->setBizContent($info);
                        $response = $aliUser->aop->pageExecute($request);
                        //htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
                        return [
                            "code" => 10,
                            "data" => htmlspecialchars_decode($response),//就是orderString 可以直接给客户端请求，无需再做处理。
                            "msg" => ['success' => '支付成功', 'error' => '支付失败']
                        ];
                    }
                } else {
                    return $this->renderError($model->getError() ?: '创建失败');
                }
            } else {
                if ($apply_id['agent']['pay_status'] === 20) {
                    return $this->renderError("您已提交申请且已支付成功代理费用，请耐心等待审核即可");
                } else {
                    if ($apply_id["pay_price"] > 1000) {
                        return json([
                            "code" => 2,
                            "msg" => "显示收款码"
                        ]);
                    } else {
                        $apply_id['province_id'] = Region::getNameById($apply_id["province_id"]);
                        $apply_id['city_id'] = Region::getNameById($apply_id["city_id"]);
                        $apply_id['region_id'] = Region::getNameById($apply_id["region_id"]);
                        $userInfo = $userModel->where("store_user_id", input('user_id'))->find();
                        $user = [
                            "wxapp_id" => 10001,
                            "open_id" => $userInfo['openid'],
                            "user_id" => $userInfo['store_user_id']
                        ];
                        $applyData = (new AgentApply())->where('apply_id', $apply_id['agent']['apply_id'])->with('agent')->find();
                        $order = [
                            "order_id" => $applyData['apply_id'],
                            "order_no" => $applyData['agent']['order_no'],
                            "pay_price" => $applyData['agent']['pay_price']
                        ];
                        if ($this->is_wechat()) {
                            $payment = $orderModel->onOrderPayment_($user, $order, 20);
                            return [
                                "code" => 3,
                                "data" => [
                                    'order_id' => $order['order_id'],   // 订单id
                                    'pay_type' => 10,  // 支付方式
                                    'payment' => $payment               // 微信支付参数
                                ],
                                "msg" => ['success' => '支付成功', 'error' => '订单未支付']
                            ];
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
                                    'body' => '代理申请',
                                    'subject' => '代理申请',
                                    'out_trade_no' => $order['order_no'],
                                    'timeout_express' => '30m',
                                    'total_amount' => $order['pay_price'],
                                    'product_code' => 'QUICK_WAP_WAY'
                                ], JSON_UNESCAPED_UNICODE);
                            $request->setNotifyUrl($this->request->domain() . "/apply.php");
                            $request->setBizContent($info);
                            $response = $aliUser->aop->pageExecute($request);
                            //htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
                            return [
                                "code" => 10,
                                "data" => htmlspecialchars_decode($response),//就是orderString 可以直接给客户端请求，无需再做处理。
                                "msg" => ['success' => '支付成功', 'error' => '支付失败']
                            ];
                        }
                    }
                }
            }
        }
        $this->view->engine->layout(false);
        $list = Db::table("zuowey_region")->where("pid", 0)->select();
        $userModel = new StoreUserModel();
        $info = $userModel->where("user_name", input('username'))->find();
        return $this->fetch('passport/select', [
            // 系统版本号
            'version' => get_version(),
            'province' => $list,
            'userinfo' => $info
        ]);
    }

    public function get_custom()
    {
        $id = input('id');
        if (empty($id)) {
            $region = explode(',', input('region'));
            $provinceId = Region::getIdByName($region[0], 1);
            $cityId = Region::getIdByName($region[1], 2, $provinceId);
            $regionId = Region::getIdByName($region[2], 3, $cityId);
            $id = $regionId;
        }
        $model = new CustomArea;
        $lists = $model->where("is_delete", 0)->where("parent_id", $id)->select();
        return $this->renderSuccess("获取成功", "", $lists);
    }

    public function pay_agent($apply_id)
    {
        // 构建支付宝请求
        $model = new AgentApply();
        $orderInfo = $model->where("apply_id", $apply_id)->with([
            'agent'
        ])->find();
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
                'body' => '省市区代理申请',
                'subject' => '省市区代理申请',
                'out_trade_no' => $orderInfo['agent']['order_no'],
                'timeout_express' => '30m',
                'total_amount' => $orderInfo['agent']['pay_price'],
                'product_code' => 'QUICK_WAP_WAY'
            ], JSON_UNESCAPED_UNICODE);
        $request->setNotifyUrl($this->request->domain() . "/apply.php");
        $request->setBizContent($info);
        $response = $aliUser->aop->pageExecute($request);
        return htmlspecialchars_decode($response);
    }

    public function pay_agent_wechat($apply_id)
    {
        // 构建微信请求
        $model = new AgentApply();
        $orderInfo = $model->where("apply_id", $apply_id)->with([
            'agent'
        ])->find();
    }

    public function get_setting($key)
    {
        $setting_model = new SettingModel;
        $data = $setting_model::getAll('10001')['agent']['values'];
        try {
            $data = $data[$key];
        } catch (\Exception $e) {
            throw new BaseException(['msg' => $e->getMessage()]);
        }
        return $this->renderSuccess('数据获取成功', '', $data);
    }

    public function get_region()
    {
        $id = input('id');
        $list = Db::table("zuowey_region")->where("pid", $id)->select();
        return $this->renderSuccess("获取成功", "", $list);
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        Session::clear('zuowey_store');
        $this->redirect('passport/login');
    }

}
