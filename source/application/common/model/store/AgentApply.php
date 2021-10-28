<?php
namespace app\common\model\store;

use app\common\model\BaseModel;

class AgentApply extends BaseModel{
    /**
     * 申请状态
     * @var array
     */
    public $applyStatus = [
        10 => '待审核',
        20 => '审核通过',
        30 => '驳回',
    ];

    /**
     * 获取器：申请时间
     * @param $value
     * @return false|string
     */
    public function getApplyTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    /**
     * 获取器：审核时间
     * @param $value
     * @return false|string
     */
    public function getAuditTimeAttr($value)
    {
        return $value > 0 ? date('Y-m-d H:i:s', $value) : 0;
    }
    
    /**
     * 代理申请信息
     * @param $where
     * @return AgentApply|null
     * @throws \think\exception\DbException
     */
    public static function detail($where)
    {
        return self::get($where);
    }

    public function agent(){
        return $this->hasOne("app\\common\\model\\AgentWith","agent_with_id","agent_with_id");
    }
}