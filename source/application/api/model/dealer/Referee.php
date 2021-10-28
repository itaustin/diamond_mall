<?php

namespace app\api\model\dealer;

use app\common\model\dealer\Referee as RefereeModel;

/**
 * 分销商推荐关系模型
 * Class Apply
 * @package app\api\model\dealer
 */
class Referee extends RefereeModel
{
    public $data = [];
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [];

    /**
     * 创建推荐关系
     * @param $user_id
     * @param $referee_id
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public static function createRelation($user_id, $referee_id)
    {
        // 分销商基本设置
        $setting = Setting::getItem('basic');
        // 是否开启分销功能
        if (!$setting['is_open']) {
            return false;
        }
        // 自分享
        if ($user_id == $referee_id) {
            return false;
        }
        // # 记录一级推荐关系
        // 判断当前用户是否已存在推荐关系
        if (self::isExistReferee($user_id)) {
            return false;
        }
        // 判断推荐人是否为分销商
        if (!User::isDealerUser($referee_id)) {
            $model = new self;
            $model->notDealerAdd($referee_id, $user_id, 1);
            // # 记录二级推荐关系
            if ($setting['level'] >= 2) {
                // 二级分销商id
                $referee_2_id = self::getRefereeUserId($referee_id, 1, false);
                // 新增关系记录
                $referee_2_id > 0 && $model->notDealerAdd($referee_2_id, $user_id, 2);
            }
            // # 记录三级推荐关系
            if ($setting['level'] == 3) {
                // 三级分销商id
                $referee_3_id = self::getRefereeUserId($referee_id, 2, false);
                // 新增关系记录
                $referee_3_id > 0 && $model->notDealerAdd($referee_3_id, $user_id, 3);
            }
            return false;
        }
        // 新增关系记录
        $model = new self;
        $model->add($referee_id, $user_id, 1);
        // # 记录二级推荐关系
        if ($setting['level'] >= 2) {
            // 二级分销商id
            $referee_2_id = self::getRefereeUserId($referee_id, 1, true);
            // 新增关系记录
            $referee_2_id > 0 && $model->add($referee_2_id, $user_id, 2);
        }
        // # 记录三级推荐关系
        if ($setting['level'] == 3) {
            // 三级分销商id
            $referee_3_id = self::getRefereeUserId($referee_id, 2, true);
            // 新增关系记录
            $referee_3_id > 0 && $model->add($referee_3_id, $user_id, 3);
        }
        return true;
    }

    /**
     * 新增关系记录
     * @param $dealer_id
     * @param $user_id
     * @param int $level
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    private function add($dealer_id, $user_id, $level = 1)
    {
        // 新增推荐关系
        $wxapp_id = self::$wxapp_id;
        $create_time = time();
        $this->insert(compact('dealer_id', 'user_id', 'level', 'wxapp_id', 'create_time'));
        // 记录分销商成员数量
        User::setMemberInc($dealer_id, $level);
        return true;
    }

    /**
     * 非分销商的关系记录
     * @param $dealer_id
     * @param $user_id
     * @param int $level
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    private function notDealerAdd($dealer_id, $user_id, $level = 1)
    {
        // 新增推荐关系
        $wxapp_id = self::$wxapp_id;
        $create_time = time();
        $this->insert(compact('dealer_id', 'user_id', 'level', 'wxapp_id', 'create_time'));
        if(User::detail($dealer_id)){
            User::setMemberInc($dealer_id, $level);
        }
        return true;
    }

    /**
     * 是否已存在推荐关系
     * @param $user_id
     * @return bool
     * @throws \think\exception\DbException
     */
    private static function isExistReferee($user_id)
    {
        return !!self::get(['user_id' => $user_id]);
    }

    /**
     * @param $referee
     * @param $user_id
     * @return array|int
     * @throws \think\exception\DbException
     */
    public function getDealerNotUserId($referee , $user_id){
        if($user_id == 0) return 0;
        foreach ($referee as $key => $value){
            if($value['user_id'] == $user_id && $value['level'] == 1){
                if($value['dealer_id'] !== 0){
                    array_push($this->data,$value['dealer_id']);
                    self::getDealerNotUserId($referee,$value['dealer_id']);
                }
            }
        }
        return $this->data;
    }

}