<?php
/**
 * date: 2020/2/22 10:11 下午
 * @author 佐威网络科技-PaulAustin
 * @version v1.0
 * Description:
 */
namespace app\mobile\controller;

use app\api\model\dealer\Referee;
use app\api\model\dealer\Setting;
use app\api\model\User;
use app\common\model\PointsCaptial;
use think\Cache;
use think\Session;
use app\api\model\Order;
vendor('aop.AopClient');

class Mine extends Controller{

    public function _initialize()
    {
        parent::_initialize();
        Session::set("uri",$this->request->domain()."/?s=/mobile/passport/login");
    }

    public function index(){
        return $this->fetch("index");
    }

    public function getCode(){
        return Cache::get("code");
    }

    public function dealer(){
        return $this->fetch("dealer");
    }

    public function withdraw(){
        return $this->fetch("withdraw");
    }

    public function order(){
        return $this->fetch("order");
    }

    public function team(){
        return $this->fetch("team");
    }

    public function apply(){
        return $this->fetch("apply");
    }

    public function changeaccount(){
        return $this->fetch("alipay");
    }

    public function check_dealer_status(){
        $user_id = input('user_id');
        $referee = Referee::where([
            'dealer_id' => $user_id,
            'level' => '1'])->select();
        $referee_count = 0;
        foreach ($referee as $key => $value){
            if($this->checkIsHave($value['user_id'])){
                $referee_count++;
            }
        }
        $order_count = Order::where([
            'user_id' => $user_id,
            'pay_status' => 20
        ])->count();
        $rule = Setting::getItem('rule');
        if($referee_count >= $rule['member'] && $order_count >= $rule['order']){
            return [
                'status' => true,
            ];
        }else{
            return [
                'status' => false
            ];
        }
    }

    public function checkIsHave($uid){
        return Order::where(['user_id' => $uid,'pay_status' => 20])->find() ? true : false;
    }

    public function points(){
        if($this->request->isAjax()){
            $model = new PointsCaptial();
            $user = (new User())::getUser(input('token'));
            $data = $model->getList($user['user_id']);
            return $this->renderSuccess("获取成功",$user,$data);
        }
        return $this->fetch();
    }
}