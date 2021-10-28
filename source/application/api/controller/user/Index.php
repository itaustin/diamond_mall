<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\dealer\Referee;
use app\api\model\User as UserModel;
use app\api\model\Order as OrderModel;
use app\api\model\Setting as SettingModel;
use app\common\exception\BaseException;
use think\Session;

/**
 * 个人中心主页
 * Class Index
 * @package app\api\controller\user
 */
class Index extends Controller
{
    /**
     * 获取当前用户信息
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function detail()
    {
//        $loginData = Session::get('zuowey_mobile');
//        if(!empty($loginData)){
//            $user = $loginData['detail'];
//        } else {
//            throw new BaseException(['code' => 0, 'msg' => '登录状态有误']);
//        }
        // 当前用户信息
        $user = $this->getUser(false);
        // 订单总数
        $model = new OrderModel;
        return $this->renderSuccess([
            'userInfo' => $user,
            'orderCount' => [
                'payment' => $model->getCount($user, 'payment'),
                'received' => $model->getCount($user, 'received'),
                'comment' => $model->getCount($user, 'comment'),
            ],
            'setting' => [
                'points_name' => SettingModel::getPointsName(),
            ],
            'menus' => (new UserModel)->getMenus()   // 个人中心菜单列表
        ]);
    }

    public function checkGrade(){
        //当前用户信息
        $user = $this->getUser(false);
        $setting = SettingModel::getItem('teamcommission',10001);
        //检测2号商城满足
        $refereeModel = new Referee();
        $pushPeopleCount = $refereeModel->alias('r')->where([
            'dealer_id' => $user['user_id'],
            'level' => 1
        ])->join('user u','r.user_id = u.user_id')->where('u.pay_money','>',0)->count();
        $directPeopleCount = $refereeModel->alias('r')->where([
            'dealer_id' => $user['user_id'],
            'level' => 2
        ])->join('user u','r.user_id = u.user_id')->where('u.pay_money','>',0)->count();
        //检测1号商城团队
        $peopleCount = 0;
        foreach ($refereeModel->where([
            'dealer_id' => $user['user_id'],
            'level' => 1
        ])->select() as $key => $value){
            if((new UserModel())->where('user_id',$value['user_id'])->field('pay_money')->find()['pay_money'] > 0){
                $peopleCount++;
            }
        }
        return [
            "first" => $this->checkFirstLevel($setting,$pushPeopleCount,$directPeopleCount),
//            "second" => $this->checkSecondLevel($setting,$pushPeopleCount,$directPeopleCount),
            "peopleCount" => $peopleCount
        ];
    }

    public function checkFirstLevel($setting,$push,$direct){
        if($push >= $setting['1']['first_team']['level_four']['first'] && ($push+$direct) >= $setting['1']['first_team']['level_four']['second']){
            return 4;
        }else if($push >= $setting['1']['first_team']['level_three']['first'] && ($push+$direct) >= $setting['1']['first_team']['level_three']['second']){
            return 3;
        }else if($push >= $setting['1']['first_team']['level_two']['first'] && ($push+$direct) >= $setting['1']['first_team']['level_two']['second']){
            return 2;
        }else if($push >= $setting['1']['first_team']['level_one']['first'] && ($push+$direct) >= $setting['1']['first_team']['level_one']['second']){
            return 1;
        }else{
            return 0;
        }
    }

    public function checkSecondLevel($setting,$push,$direct){
        if($push >= $setting['2']['first_team']['level_four']['first'] && ($push+$direct) >= $setting['2']['first_team']['level_four']['second']){
            return "[二号商城]四星会员";
        }else if($push >= $setting['2']['first_team']['level_three']['first'] && ($push+$direct) >= $setting['2']['first_team']['level_three']['second']){
            return "[二号商城]三星会员";
        }else if($push >= $setting['2']['first_team']['level_two']['first'] && ($push+$direct) >= $setting['2']['first_team']['level_two']['second']){
            return "[二号商城]二星会员";
        }else if($push >= $setting['2']['first_team']['level_one']['first'] && ($push+$direct) >= $setting['2']['first_team']['level_one']['second']){
            return "[二号商城]一星会员";
        }else{
            return "[二号商城]未达到会员标准";
        }
    }

}
