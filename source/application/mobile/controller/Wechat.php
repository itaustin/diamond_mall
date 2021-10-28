<?php
/**
 * date: 2020/2/23 4:32 下午
 * @author 佐威网络科技-PaulAustin
 * @version v1.0
 * Description:
 */
namespace app\mobile\controller;

use app\api\model\User;
use think\Controller;
use think\Session;

class Wechat extends Controller{
    public function getCode(){
        $code = $this->request->param('code');
        $uri = Session::get("uri")."&code=".$code;
        $model = new User();
        $userinfo = $model->get_userinfo($code);
        if(!empty($userinfo)){
            $this->redirect("/?s=/mobile/passport/login");
        }
    }

    public function getcode_register(){
        $code = $this->request->param('code');
        $register_code = $this->request->param('register_code');
        $model = new User();
        $userinfo = $model->get_userinfo($code);
        if(!empty($userinfo)){
            $uri = "/?s=/mobile/passport/register&code=" . $register_code;
            $this->redirect($uri);
        }
    }

    public function getcode_store_register(){
        $code = $this->request->param('code');
        $model = new User();
        $userinfo = $model->get_userinfo($code);
        if(!empty($userinfo)){
            $uri = "/?s=/store/passport/register";
            $this->redirect($uri);
        }
    }
}