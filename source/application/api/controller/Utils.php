<?php


namespace app\api\controller;

// 工具类
use app\api\model\HuaweiSms;
use think\Cache;
use think\Controller;

class Utils extends Controller
{
    public function getSms($mobile_phone,$randCode){
        $huaweiSmsModel = new HuaweiSms();
        $smsData = [
            'sms_phone' => $mobile_phone,
            'code'  =>  $randCode
        ];
        $result = $huaweiSmsModel->sendsms($smsData);
        if((int)$result['code'] === 000000){
            Cache::set("sms_".$smsData['sms_phone'],$smsData['code'],"300");
            return ["code" => 1,"msg" => "发送成功"];
        }
        return ["code" => 0,"msg" => "发送失败，请检查"];
    }

    public static function generate_code($length = 6) {
        return rand(pow(10,($length-1)), pow(10,$length)-1);
    }
}