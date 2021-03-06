<?php

namespace app\api\controller;

use app\api\model\dealer\Referee;
use app\api\model\User as UserModel;
use app\api\model\Order as OrderModel;
use app\api\model\UserReferee;
use app\common\exception\BaseException;
use app\common\model\GoldCoupon;
use app\common\model\PointsCaptial;
use think\Cache;
use think\Cookie;
use think\Session;

/**
 * 用户管理
 * Class User
 * @package app\api
 */
class User extends Controller
{
    /**
     * 用户自动登录
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function login()
    {
        $model = new UserModel;
        return $this->renderSuccess([
            'user_id' => $model->login($this->request->post()),
            'token' => $model->getToken()
        ]);
    }

    public function userInfo(){
        $this->user['token'] = input('token');
        $userInfo = $this->getUser();
        if(!strpos($userInfo['alipay_img'], "img.zuowey.com")) {
            $userInfo['alipay_img'] = base_url() . $userInfo['alipay_img'];
        }
        if(!strpos($userInfo['wechat_img'], "img.zuowey.com")) {
            $userInfo['wechat_img'] = base_url() . $userInfo['wechat_img'];
        }
        return json($this->renderSuccess($userInfo));
    }

    public function changeNickName(){
        $this->user['token'] = input('token');
        $userInfo = $this->getUser();
        $model = new UserModel();
        $nickName = trim(input("nickName"));
        if(empty($nickName)){
            return $this->renderError("请填写昵称");
        }
        $model->startTrans();
        try {
            $model->where("user_id", $userInfo['user_id'])->update(["nickName" => input("nickName")]);
            $model->commit();
            return $this->renderSuccess("","修改成功");
        } catch (\Exception $exception){
            return $this->renderError($exception->getMessage(), "");
        }
    }

    public function getQrcode(){
        $userInfo = $this->detail();
        $url = $this->request->domain()."/?s=/mobile/qrcode/view&mobile=".$userInfo['data']['userInfo']['mobile_phone']."&url=".$this->request->domain()."/?s=/mobile/passport/register--code=".$userInfo['data']['userInfo']['mobile_phone'];
        return $this->renderSuccess('获取成功',$url);
    }

    public function changeAliPay(){
        $input_code = input('code');
        $model = new UserModel();
        $userInfo = $model->where([
            'user_id' => input('user_id')
        ])->find();
        $code = Cache::get('sms_'.$userInfo['mobile_phone']);
        if(empty($input_code)){
            return $this->renderError("请输入手机验证码");
        }
        if($userInfo->allowField(true)->save([
            'alipay_account' => input('alipay_account'),
            'alipay_name' => input('alipay_name'),
            'mobile_phone' => input('mobile_phone'),
            'nickName' => input('nickName')
        ])){
            return $this->renderSuccess("",'支付宝账号修改成功');
        }
        return $this->renderError('支付宝账号修改失败');
        if($input_code == $code){

        }else{
            return $this->renderError("手机验证码错误");
        }
    }
    
    public function countAll(){
        $user = $this->detail()['data']['userInfo'];
        $userModel = new UserModel();
        $refereeModel = new Referee();
        $count =  $refereeModel->with(['dealer', 'user'])
            ->alias('referee')
            ->field('referee.*')
            ->join('user', 'user.user_id = referee.user_id')
            ->where('referee.dealer_id', '=', $user['user_id'])
            ->where('referee.level', 'in', '1,2')
            ->where('user.is_delete', '=', 0)
            ->where('user.pay_money','>',0)
            ->order(['referee.create_time' => 'desc'])
            ->count();
        $firstCount = $refereeModel->with(['dealer', 'user'])
            ->alias('referee')
            ->field('referee.*')
            ->join('user', 'user.user_id = referee.user_id')
            ->where('referee.dealer_id', '=', $user['user_id'])
            ->where('referee.level', '=', '1')
            ->where('user.is_delete', '=', 0)
            ->where('user.pay_money','>',0)
            ->order(['referee.create_time' => 'desc'])
            ->count();
        return [$count,$firstCount];
    }

    public function checkIsPay($user_id){
        $model = new UserModel();
        if($model->where('user_id',$user_id)->field('pay_money')->find()['pay_money'] > 0){
            return true;
        }
        return false;
    }

    public function tokenSendMsg(){
        $model = new UserModel();
        $mobile_phone = $model->where('user_id',input('user_id'))->field('mobile_phone')->find()['mobile_phone'];
        $randCode = self::generate_code(4);
        $smSdk = new \app\mobile\controller\Sms();
        $data = [
            'sms_phone' => $mobile_phone,
            'code'  =>  $randCode
        ];
        $result = $smSdk->sendsms($data);
        Cache::set("sms_".input('mobile_phone'),$randCode,"300");
        return $result['code'] == 000000 ? ["code" => 1,"msg" => "发送成功","code_num" => Cache::get("sms_".$mobile_phone)] : ["code" => 0,"msg" => "发送失败，请检查"];
    }

    public function checkRegisterCoder(){
        $code = input('code');
        $model = new UserModel();
        $userInfo = $model->where('mobile_phone',$code)->find();
        $orderModel = new \app\api\model\Order();
        $result = $orderModel->where([
            "user_id" => $userInfo['user_id'],
            "pay_status" => 20
        ])->count();
        if($userInfo && $result > 0){
            return $this->renderSuccess($code,'邀请码有效');
        }
        return $this->renderError('邀请码无效');
    }

    public function register(){
        $model = new UserModel();
        return $this->renderSuccess([
            'user_id' => $model->register_ali($this->request->post()),
            'token' => $model->getToken()
        ]);
    }

    public function recover(){
        $param = $this->request->param();
        $code = Cache::get('sms_'.$param['mobile_phone']);
        if($code == $param['code']){
            $model = new UserModel();
            if($model->where('username',$param['mobile_phone'])->find()){
                if($model->where('username',$param['mobile_phone'])->update([
                    'password' => zuowey_hash($param['password'])
                ])){
                    return ['code' => 1,'msg' => '密码修改成功'];
                }else{
                    return ['code' => 0,'msg' => '密码未修改'];
                }
            }else{
                return ['code' => 0,'msg' => '未查询到该账号'];
            }
        }else{
            return ['code' => 0,'msg' => '手机验证码有误，请核实'];
        }
    }

    public function checkUser(){
        $cookie = Cookie::get('user_info');
        $model = new UserModel();
        if(empty($cookie)){
            $code = input('code');
            $result = $model->get_userinfo($code);
        }else{
            return $this->renderSuccess([
                'user_id' => $model->login_typetwo($cookie),
                'token' => $model->getToken()
            ]);
        }
        if(!empty($result)){

        }else{
            throw new BaseException(['code' => 0,'msg' => '未查找到您的账户，请您点击右上角进行注册']);
        }
    }

    public function passlogin(){
        $param = $this->request->param();
        $param['password'] = zuowey_hash($param['password']);
        $model = new UserModel();
        return $this->renderSuccess([
            'user_id' => $model->login_pass($param),
            'token' => $model->getToken()
        ]);
    }

    public function alipay_userinfo(){
        $model = new UserModel();
        $result = $model->alilogin(input('auth_code'));
        Cookie::set('ali_userinfo',$result);
        return $result;
    }

    public function sendMsg(){
        $randCode = self::generate_code(4);
        $smSdk = new \app\mobile\controller\Sms();
        $data = [
            'sms_phone' => input('mobile_phone'),
            'code'  =>  $randCode
        ];
        $result = $smSdk->sendsms($data);
        Cache::set("sms_".input('mobile_phone'),$randCode,"300");
        return $result['code'] == 000000 ? ["code" => 1,"msg" => "发送成功"] : ["code" => 0,"msg" => "发送失败，请检查"];
    }

    protected static function generate_code($length = 6) {
        return rand(pow(10,($length-1)), pow(10,$length)-1);
    }

    /**
     * 当前用户详情
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function detail()
    {
        // 当前用户信息
        $userInfo = $this->getUser();
        return $this->renderSuccess(compact('userInfo'));
    }

    public function team(){
        $model = new UserReferee();
        $orderModel = new OrderModel();
        $this->user['token'] = input('token');
        $user = $this->getUser();
        $GLOBALS['all_user'] = [];
        $this->getFirst($user['user_id']);
        $user_ids = "";
        foreach ($GLOBALS['all_user'] as $value) {
            $user_ids .= $value["user_id"] . ",";
        }
        $user_ids = $user_ids . $user["user_id"];
        $team_total_price = $orderModel
            ->where("pay_status", 20)
            ->where("user_id", "in", $user_ids)
            ->where("order_status", "not in", "20,21")
            ->where("is_delete", 0)
            ->sum("pay_price");
        $allReferee = $model
            ->with(['user'])
            ->where("level",input("level"))
            ->where("dealer_id",$user['user_id'])->select();
        $firstCount = $model
            ->where("level",1)
            ->where("dealer_id",$user['user_id'])->count();
        $secondCount = $model
            ->where("level",2)
            ->where("dealer_id",$user['user_id'])->count();
        $totalCount = $firstCount + $secondCount;
        $orderModel = new OrderModel();
        foreach ($allReferee as &$value){
            $value["user"]["username"] = substr_replace($value["user"]["username"], "****", 3, 4);
            $order = $orderModel->with(['goods'])
                ->where("user_id",$value['user']['user_id'])
                ->where("order_status", "not in", "20,21")
                ->where("pay_status", 20)
                ->select();
            $count = 0;
            foreach ($order as $k => $v) {
                $count++;
            }
            $value['order_count'] = $count;

            $value['order_total_price'] = $orderModel
                ->where("user_id",$value['user']['user_id'])
                ->where("order_status", "not in", "20,21")
                ->where("pay_status", 20)
                ->sum("pay_price");
        }
        return $this->renderSuccess([
            "referee" => $allReferee,
            "count" => [
                "first" => $firstCount,
                "second" => $secondCount,
                "total" => count($GLOBALS['all_user']),
                "team_total_price" => $team_total_price
            ]
        ]);
    }

    public function getFirst($user_id)
    {
        $model = new UserReferee();
        $refereeData = $model->with(['users'])
            ->where("dealer_id", $user_id)
            ->where("level", 1)
            ->field("id,dealer_id, user_id")
            ->select();
        foreach ($refereeData as $value) {
            $GLOBALS['all_user'][] = $value['users']->toArray();
            $this->getFirst($value["user_id"]);
        }
    }


        /**
     * 验证姓名和身份证及昵称
     */
    public function certification(){
        $param = $this->request->param();
        $this->user['token'] = $param['token'];
        $user = $this->getUser($param['token']);
        unset($param['token']);
        $param['is_certification'] = 1;
        $model = new UserModel();
        if($model->where("user_id",$user['user_id'])->update($param)){
            return $this->renderSuccess("","操作成功");
        }
        return $this->renderError("未更新","");
    }

    public function check_is_login(){
        $sessionData = Session::get("zuowey_mobile");
        if(!empty($sessionData) && $sessionData['is_login'] == 1){
            return json($this->renderSuccess($sessionData, "已登录"));
        } else {
            return json($this->renderError("未登录"));
        }
    }

    public function authorization(){
        $userInfo = $this->getUser(true);
        $model = new UserModel;
        $param = $this->request->param();
        unset($param['token']);
        if($model->where("user_id",$userInfo['user_id'])->update($param)){
            return $this->renderSuccess("","银行卡保存成功");
        }
        return $this->renderError("未更新","");
    }

    public function customer_service(){
        return $this->renderSuccess("13220169439");
    }

    public function redeem_gold(){
        $user = $this->getUser();
        $model = new UserModel();
        $g = input("g");
        $goldCouponModel = new GoldCoupon();
        $model->startTrans();
        try {
            // 先检测手工积分是否足够
            $freeze_points = bcmul($g, $user["g_exchange_price"], 2);
            $handling_fee_points = bcmul($g, $user["g_price"], 2);
            if($user["handling_fee_points"] >= $handling_fee_points) {
                // 满足手工积分
                if($user["freeze_points"] >= $freeze_points){
                    $model->where("user_id", $user["user_id"])
                        ->setDec("freeze_points", $freeze_points);
                    $model->where("user_id", $user["user_id"])
                        ->setDec("handling_fee_points", $handling_fee_points);
                    $goldCouponModel->insert([
                        "user_id" => $user["user_id"],
                        "order_id" => 0,
                        "money" => $g,
                        "is_need_fee" => 1,
                        "create_time" => time(),
                        "update_time" => time()
                    ]);
                    $pointsCaptialModel = new PointsCaptial();
                    $pointsCaptialModel->insert([
                        "user_id" => $user['user_id'],
                        "type" => 10,
                        "order_id" => 0,
                        "points" => $freeze_points,
                        "create_time" => time(),
                        "description" => "兑换黄金，共花费" . $freeze_points . "积分、" . $handling_fee_points . "手工积分",
                        "consignment_money" => 0.00
                    ]);
                    $model->commit();
                    return $this->renderSuccess("","兑换成功");
                } else {
                    return $this->renderError("您的可用积分不足以兑换{$g}g黄金", "");
                }
            } else {
                return $this->renderError("您的手工积分不足","");
            }

        } catch (BaseException $exception) {
            return $this->renderError($exception->getMessage(),"");
        }
    }

    public function teamList(){
        $userInfo = $this->getUser();
        $GLOBALS['all_user'] = [];
        $model = new \app\api\model\Order();
        $this->getFirst($userInfo["user_id"]);
        $data = $GLOBALS['all_user'];
        $userModel = new UserModel();
        foreach ($data as &$value) {
            $GLOBALS['all_user'] = [];
            $this->getFirst($value["user_id"]);
            $user_ids = "";
            foreach ($GLOBALS['all_user'] as $vv) {
                $user_ids .= $vv["user_id"] . ",";
            }
            $price = $model
                ->where("pay_status", 20)
                ->sum("pay_price");
            $value["total_price"] = "{$price}";
            $value['username'] = substr_replace($value['username'],'****',3,4);;
        }
        return $this->renderSuccess($data, "");
    }

    public function changeLevel(){
        $user_id = input("user_id");
        $level = input("level");
        $model = new UserModel();
        $model->where("user_id", $user_id)
            ->update([
                "level" => $level,
                "is_hand" => 1
            ]);
        return $this->renderSuccess("", "等级修改成功");
    }

    public function forwardPoints(){
        $mobile_phone = input("mobile_phone");
        $userInfo = $this->getUser();
        $model = new UserModel();
        $forWardUserInfo = $model->where("username", $mobile_phone)
            ->find();
        if(empty($forWardUserInfo)){
            return $this->renderError("接收者不存在，请检查接收人手机号是否有误。", "");
        } else {
            // 如果存在
            $points_num = input("points_num");
            // 检测自己的积分是否足够
            if($userInfo["handling_fee_points"] >= $points_num) {
                $model->where("user_id", $userInfo["user_id"])->setDec("handling_fee_points", $points_num);
                $model->where("user_id", $forWardUserInfo["user_id"])->setInc("handling_fee_points", $points_num);
                return $this->renderSuccess("", "转发成功");
            } else {
                return $this->renderError("您的手工积分不足以发送给他人", "");
            }
        }
    }
}
