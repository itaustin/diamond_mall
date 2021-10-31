<?php


namespace app\api\model;

use app\common\model\UserReferee as RefereeModel;

class UserReferee extends RefereeModel
{
    /**
     * 创建推荐关系
     */
    public static function createRelation($user_id,$referee_id){
        if($referee_id == $user_id){
            return false;
        }
        $model = new static;
        // 记录推荐关系
        $model::add($referee_id, $user_id);
        // 记录二层推荐关系
        $referee_2_id = $model::getRefereeUserId($referee_id,1);
        if($referee_2_id > 0){
            $model::add($referee_2_id,$user_id,2);
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
    public static function add($dealer_id, $user_id, $level = 1)
    {
        // 新增推荐关系
        $wxapp_id = 10001;
        $create_time = time();
        (new static())->insert(compact('dealer_id', 'user_id', 'level', 'wxapp_id', 'create_time'));
        // 记录分销商成员数量
        User::setMemberInc($dealer_id, $level);
        return true;
    }
}