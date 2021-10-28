<?php

namespace app\api\controller;

use app\common\library\wechat\WxPay;

/**
 * 支付成功异步通知接口
 * Class Notify
 * @package app\api\controller
 */
class Notify extends \think\Controller
{
    /**
     * 支付成功异步通知
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function order()
    {
        // 微信支付组件：验证异步通知
        $WxPay = new WxPay();
        $WxPay->notify($this->request->param());
    }

    public function apply()
    {
        // 微信支付组件：验证异步通知
        $WxPay = new WxPay();
        $WxPay->apply_notify($this->request->param());
    }

    public function store(){
        $WxPay = new WxPay();
        $WxPay->apply_admin($this->request->param());
    }

    public function alipay(){
        $AliPay = new WxPay();
        $AliPay->notify_alipay($this->request->param());
    }
}
