<?php
/**
 * date: 2020/5/18 3:28 下午
 * @author 佐威网络科技-PaulAustin
 * @version v1.0
 * Description:
 */
namespace app\api\model;

use think\Cache;
use think\Model;
use think\Session;

class Sms extends Model
{
    protected $username;
    protected $password;

    public function initialize()
    {
        parent::initialize();
        $setting = Setting::getSetting(["key"=>"mobilecode"])["values"];
        $this->username = $setting["username"];
        $this->password = $setting["password"];
    }

    protected static function randCode(){
        // 密码字符集，生成随机6位数验证码
        $chars = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        // 在 $chars 中随机取 $length 个数组元素键名
        $keys = array_rand($chars, 6);
        $password = '';
        for($i = 0; $i < 6; $i++)
        {
            // 将 $length 个数组元素连接成字符串
            $password .= $chars[$keys[$i]];
        }
        return $password;
    }

    protected static function generate_code($length = 6) {
        return rand(pow(10,($length-1)), pow(10,$length)-1);
    }


    protected static function curlGetCode($url,$data){
        return curlGet($url,$data);
    }

    public function getCodeResult($post){
        $randCode = self::generate_code(6);
        $smSdk = new \app\mobile\controller\Sms();
        $data = [
            'sms_phone' => $post['phone'],
            'code'  =>  $randCode
        ];
        $result = $smSdk->sendsms($data);
        Cache::set("smscode",$randCode,"300");
        return $result['code'] == 000000 ? ["code" => 1,"msg" => "发送成功"] : ["code" => 0,"msg" => "发送失败，请检查"];
        $data = [
            'name' => $this->username,
            'pwd'  => $this->password,
            'dst'  => $post['phone'],
            'msg'  => $randCode."（懒人街，请勿泄露）,".$post['phone']."正在注册，如有问题，请联系客服，该验证5分钟后过期，请尽快验证",
            'time' => ''
        ];
        $result = self::curlGetCode("http://180.76.112.107/send/gsendv2_utf8.asp",$data);
        $stringToArrayFirst = explode("&",$result);
        $str = "";
        foreach ($stringToArrayFirst as $value){
            $str .= $value."=";
        }
        $sencond =  explode("=",$str);
//        return $sencond;
        $returnData = [
//            "err"   => $sencond["7"],
            "err"   => $sencond["1"],
            "errid" => $sencond["9"],
            "msgid" => $sencond["11"]
        ];
        if($returnData["errid"] == 0){
            Session::set("smscode",$randCode);
            return $returnData["err"] == 0 ? ["code" => $sencond["1"],"msg" => "发送失败，请检查"] : ["code" => $sencond["1"],"msg" => "发送成功"];
        }
    }
}
