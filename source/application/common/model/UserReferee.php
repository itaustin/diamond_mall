<?php


namespace app\common\model;


class UserReferee extends BaseModel
{

    /**
     * 关联用户表
     * @return \think\model\relation\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('app\api\model\User');
    }

    /**
     * 关联用户表
     * @return \think\model\relation\BelongsTo
     */
    public function users()
    {
        return $this->belongsTo('app\api\model\User')->field("user_id,real_name,alipay_name,nickName,username");
    }

    /**
     * 获取上级用户id
     * @param $user_id
     * @param $level
     * @param bool $is_dealer 必须是分销商
     * @return bool|mixed
     * @throws \think\exception\DbException
     */
    public static function getRefereeUserId($user_id, $level)
    {
        $dealer_id = (new self)->where(compact('user_id', 'level'))
            ->value('dealer_id');
        return !empty($dealer_id) ? $dealer_id : 0;
    }
}