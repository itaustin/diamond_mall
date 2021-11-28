<?php


namespace app\api\controller;


use app\api\model\User;
use app\api\model\UserReferee;
use app\common\exception\BaseException;
use app\api\controller\InviteCode;
use app\api\controller\Utils;
use think\Cache;

class Passport extends Controller
{
    /**
     * 登录界面
     */
    public function login(){
        // 登录操作
        $param = $this->request->param();
        // 验证完成后，删除验证码
        $param['password'] = zuowey_hash($param['password']);
        $model = new User($param);
        return json($this->renderSuccess([
            'user_id' => $model->login($param),
            'token' => $model->getToken()
        ],"登录成功"));
    }

    public function register(){
        $param = $this->request->param();
        $active_code = Cache::get('sms_'.$param['username']);
        // 初始化模型
        $inviteUtil = new InviteCode();
        $param['active_code'] = trim($param['active_code']);
        $model = new User();
        $model->startTrans();
        try {
            if($param['active_code'] == $active_code){
                // 开始查找邀请码是否存在
                $refereeId = $model->where("active_code",$param['referee_code'])->where("is_delete",0)->find();
                if(empty($refereeId)){
                    return $this->renderError("邀请人不存在或被删除，请核实。","");
                }
                // 密码正确，开始检测重复密码是否正确
                if($param['password'] !== $param['repeat_password']){
                    return $this->renderError("请确认重复密码是否正确","");
                }
                // 检测账号是否被注册
                if($model->where("username",$param['username'])->find()){
                    return $this->renderError("账号已经被注册","");
                }
                // 重复密码正确，开始组装内容
                $registerParam = [
                    "username" => $param['username'],
                    "password" => zuowey_hash($param['password']),
                    "nickName" => "会员",
                    "gender" => "0",
                    "create_time" => time(),
                    "update_time" => time()
                ];
                $model->save($registerParam);
                UserReferee::createRelation($model['user_id'],$refereeId['user_id']);
                $model->where("user_id",$model['user_id'])->update([
                    "active_code" => $inviteUtil->encode($model['user_id'])
                ]);
                $model->where("user_id", $model["user_id"])
                    ->update([
                        "is_leaf" => 1
                    ]);
                $GLOBALS['allParentUserIds'] = [];
                $this->getTopLine($model["user_id"]);
                $data = $GLOBALS['allParentUserIds'];
                foreach ($data as $value) {
                    $model->where("user_id", $value)
                        ->update([
                            "is_leaf" => 0
                        ]);
                }
                $model->commit();
                return $this->renderSuccess("","注册成功...");
            } else {
                return $this->renderError("手机验证码错误","");
            }
        } catch (\Exception $e){
            throw new BaseException(["msg" => $e->getMessage()]);
        }
    }

    /**
     * @description 获取推荐关系的一条线
     * @param $user_id
     */
    public function getTopLine($user_id){
        $model = new UserReferee();
        // 找到自己的上级
        $dealer_id = $model->where("user_id", $user_id)
            ->where("level", 1)
            ->value("dealer_id");
        if(!empty($dealer_id)){
            $GLOBALS['allParentUserIds'][] = $dealer_id;
            $this->getTopLine($dealer_id);
        }
    }

    /**
     * 获取手机验证码
     */
    public function getGenerateCode(){
        // 初始化工具类
        $utils = new Utils();
        // 生成随机码
        $randCode = $utils::generate_code(4);
        $mobile_phone = input('mobile_phone');
        // 发送手机验证码
        return $utils->getSms($mobile_phone,$randCode);
    }

    public function changePassword(){
        $model = new User();
        $param = $this->request->param();
        $this->user['token'] = input('token');
        $userInfo = $this->getUser();
        $checkFind = $model->where("user_id", $userInfo['user_id'])
            ->where("password", zuowey_hash($param['password']))
            ->find();
        if(!empty($checkFind)){
            if($param['new_password'] == $param['repeat_new_password']){
                $model->where("user_id", $checkFind['user_id'])
                    ->update([
                        "password" => zuowey_hash($param['new_password'])
                    ]);
                return $this->renderSuccess("","密码修改成功");
            } else {
                return $this->renderError("两次新密码不一致, 请检查","");
            }
        }
        return $this->renderError("原密码错误","");
    }

    public function forget(){
        $userModel = new User();
        $param = $this->request->param();
        $active_code = Cache::get('sms_'.$param['username']);
        if($param['active_code'] == $active_code){
            $userInfo = $userModel->where("username", $param['username'])->find();
            if(empty($userInfo)){
                return $this->renderError("该用户未注册，请确认","");
            }
            if($param['password'] !== $param['repeat_password']){
                return $this->renderError("请确认重复密码是否正确","");
            }
            $userModel->where("user_id", $userInfo['user_id'])
                ->update([
                    "password" => zuowey_hash($param['password'])
                ]);
            return $this->renderSuccess("","找回密码成功");
        } else {
            return $this->renderError("手机验证码错误","");
        }
    }
}