<?php

namespace app\api\controller\user\dealer;

use app\api\controller\Controller;
use app\api\model\dealer\Setting;
use app\api\model\dealer\User as DealerUserModel;
use app\api\model\dealer\Referee as RefereeModel;

/**
 * 我的团队
 * Class Order
 * @package app\api\controller\user\dealer
 */
class Team extends Controller
{
    /* @var \app\api\model\User $user */
    private $user;

    private $dealer;
    private $setting;

    /**
     * 构造方法
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function _initialize()
    {
        parent::_initialize();
        // 用户信息
        $this->user = $this->getUser();
        // 分销商用户信息
        $this->dealer = DealerUserModel::detail($this->user['user_id']);
        // 分销商设置
        $this->setting = Setting::getAll();
    }

    /**
     * 我的团队列表
     * @param int $level
     * @return array
     * @throws \think\exception\DbException
     */
    public function lists($level = -1)
    {
        $model = new RefereeModel;
        return $this->renderSuccess([
            // 分销商用户信息
            'dealer' => $this->dealer,
            // 我的团队列表
            'list' => $model->getList($this->user['user_id'], (int)$level),
            // 基础设置
            'setting' => $this->setting['basic']['values'],
            // 页面文字
            'words' => $this->setting['words']['values'],
            
            'has' => $this->hasPeople($this->user['user_id'], (int)$level),
            'hasSecond' => $this->hasPeople($this->user['user_id'],2)
        ]);
    }
    
    public function hasPeople($user_id,$level = -1){
        $model = new RefereeModel();
        if($level = 2){
            $model->where('referee.level','in','1,2');
        }else{
            $level > -1 && $model->where('referee.level', '=', $level);
        }
        return $model->with(['dealer', 'user'])
            ->alias('referee')
            ->field('referee.*')
            ->join('user', 'user.user_id = referee.user_id')
            ->where('referee.dealer_id', '=', $user_id)
            ->where('user.is_delete', '=', 0)
            ->where('user.pay_money','>',0)
            ->order(['referee.create_time' => 'desc'])
            ->count();
    }

}