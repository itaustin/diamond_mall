<?php
namespace app\common\model;

class AgentWithdraw extends BaseModel{

    /**
     * 打款方式
     * @var array
     */
    public $payType = [
        10 => '微信',
        20 => '支付宝',
        30 => '银行卡',
    ];

    /**
     * 申请状态
     * @var array
     */
    public $applyStatus = [
        10 => '待审核',
        20 => '审核通过',
        30 => '驳回',
        40 => '提现成功',
    ];

    /**
     * 关联分销商用户表
     */
    public function user()
    {
        return $this->belongsTo('app\common\model\store\User','store_user_id','user_id');
    }

    /**
     * 提现详情
     * @param $id
     */
    public static function detail($id)
    {
        return self::get($id);
    }
}