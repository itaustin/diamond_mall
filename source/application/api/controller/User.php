<?php

namespace app\api\controller;

use app\api\model\dealer\Referee;
use app\api\model\User as UserModel;
use app\common\exception\BaseException;
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

    public function check_is_login(){
        $sessionData = Session::get("zuowey_mobile");
        if(!empty($sessionData) && $sessionData['is_login'] == 1){
            return json($this->renderSuccess($sessionData, "已登录"));
        } else {
            return json($this->renderError("未登录"));
        }
    }

}
