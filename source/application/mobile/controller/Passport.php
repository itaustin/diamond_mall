<?php
/**
 * date: 2020/5/18 2:31 下午
 * @author 佐威网络科技-PaulAustin
 * @version v1.0
 * Description:
 */
namespace app\mobile\controller;

use think\Cookie;

class Passport extends Controller
{
    public function login(){
        $wechat_info = Cookie::get('user_info');
        if(empty($wechat_info) && $this->is_wechat()){
            $this->redirect("https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx38d44f6a1bc6904d&redirect_uri=https://paimaimall.zuowey.com/?s=/mobile/wechat/getCode&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect");
        }
        return $this->fetch('login');
    }

    public function register(){
        $code = input('code');
        $userinfo = Cookie::get('user_info');
        if(empty($userinfo) && $this->is_wechat()){
            $this->redirect('https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx38d44f6a1bc6904d&redirect_uri=https://paimaimall.zuowey.com/index.php?s=/mobile/wechat/getcode_register/register_code/'.$code.'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect',301);
        }
        return $this->fetch('register');
    }

    public function recover(){
        return $this->fetch('recover');
    }
}