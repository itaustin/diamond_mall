<?php

namespace app\common\library\wechat;

use think\Cache;

/**
 * 微信公众号用户管理类
 * Class WxUser
 * @package app\common\library\wechat
 */
class WxUser extends WxBase
{
    /**
     * 获取session_key
     * @param $code
     * @return array|mixed
     */
    public function sessionKey($code)
    {
        /**
         * code 换取 session_key
         * ​这是一个 HTTPS 接口，开发者服务器使用登录凭证 code 获取 session_key 和 openid。
         * 其中 session_key 是对用户数据进行加密签名的密钥。为了自身应用安全，session_key 不应该在网络上传输。
         */
        $url = 'https://api.weixin.qq.com/sns/jscode2session';
        $result = json_decode(curl($url, [
            'appid' => $this->appId,
            'secret' => $this->appSecret,
            'grant_type' => 'authorization_code',
            'js_code' => $code
        ]), true);
        if (isset($result['errcode'])) {
            $this->error = $result['errmsg'];
            return false;
        }
        return $result;
    }

    public function openidKey($code){
        /**
         * 换取AccessToken和openid
         */
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token";
        $getAccessToken = json_decode(curl($url, [
            'appid' => $this->appId,
            'secret' => $this->appSecret,
            'grant_type' => 'authorization_code',
            'code' => $code,
        ]),true);
        if(isset($getAccessToken['errcode'])){
            $this->error = $getAccessToken['errmsg'];
            return false;
        }
        /**
         * 刷新AccessToken，为获取用户信息做准备
         */
        $url = "https://api.weixin.qq.com/sns/oauth2/refresh_token";
        $refresh_token = json_decode(curl($url, [
            'appid' => $this->appId,
            'grant_type' => 'refresh_token',
            'refresh_token' => $getAccessToken['refresh_token'],
        ]),true);
        if(isset($refresh_token['errcode'])){
            $this->error = $refresh_token['errmsg'];
            return false;
        }
        /**
         * 换取用户个人信息
         */
        $url = "https://api.weixin.qq.com/sns/userinfo";
        $userinfo = json_decode(curl($url, [
            'access_token' => $refresh_token['access_token'],
            'openid' => $refresh_token['openid'],
            'lang' => "zh_CN",
        ]),true);
        if(isset($userinfo['errcode'])){
            $this->error = $userinfo['errmsg'];
            return false;
        }
        $result = [
            "openid" => $refresh_token['openid'],
            "user_info" => [
                "nickName" => $userinfo["nickname"],
                "gender"   => $userinfo["sex"],
                "language" => $userinfo["language"],
                "city"     => $userinfo["city"],
                "province" => $userinfo["province"],
                "country"  => $userinfo["country"],
                "avatarUrl"=> $userinfo["headimgurl"]
            ],
        ];
        return $result;
    }

}