<?php

namespace app\store\model;

use app\common\model\AgentWith as AgentWithModel;
use app\store\model\store\AgentApply;
use think\Queue;

class AgentWith extends AgentWithModel
{

    public function add($data)
    {
        $this->startTrans();
        try {
            $data["order_no"] = createOrderNo();
//            $data["pay_price"] = "0.10";
            // 新增关联记录
            $this->allowField(true)->save($data);
            $model = new AgentApply();
            $model->startTrans();
            try {
                $user = (new \app\store\model\store\User())->where("store_user_id", $data['user_id'])->find();
                $apply_data = [
                    "user_id" => $data['user_id'],
                    "agent_with_id" => $this['agent_with_id'],
                    "real_name" => $user['real_name'],
                    "mobile" => $user['mobile_phone'],
                    "description" => $data['pay_price'] <= 1000 ? '微信在线支付' : '扫码转账支付',
                    "transfer_remark" => '',
                    "pay_status" => 10,
                    "apply_time" => time(),
                    "apply_status" => 10,
                    "wxapp_id" => 10001
                ];
                $model->allowField(true)->save($apply_data);
                $model->commit();
            } catch (\Exception $e) {
                $this->rollback();
                $model->rollback();
                $this->error = $e->getMessage();
                return false;
            }
            $this->commit();
            $jobHandlerClassName = 'app\store\job\agent_job';
            $jobQueueName = "buyAgent";
            $jobData = ['ts' => time(), 'orderParam' => ["agent_with_id" => $this['agent_with_id']], 'a' => 1];
            Queue::later( 900,$jobHandlerClassName, $jobData, $jobQueueName);
            return $model;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    public function checkExist($data)
    {
        $model = new static();
        $where = [];
        if($data["identity"] == "province"){
            $where = [
                "province_id" => $data["province_id"],
                "type" => 2
            ];
        } else if($data["identity"] == "city") {
            $where = [
                "province_id" => $data["province_id"],
                "city_id" => $data["city_id"],
                "type" => 3
            ];
        } else if($data["identity"] == "region") {
            $where = [
                "province_id" => $data["province_id"],
                "city_id" => $data["city_id"],
                "region_id" => $data["region_id"],
                "type" => 4
            ];
        } else if($data["identity"] = "area"){
            $where = [
                "province_id" => $data["province_id"],
                "city_id" => $data["city_id"],
                "region_id" => $data["region_id"],
                "area_id" => $data["area_id"],
                "type" => 5
            ];
        }
        if ($with_id = $model->with(['agent'])->order("create_time DESC")
            ->where($where)
            ->where("user_id", $data["user_id"])->find()) {
            return $with_id;
        }
        return false;
    }

    /**
     * 检测并检索相应区域是否有在线申请
     * @param $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function initSearch($data)
    {
        $identity = $data["identity"];
        switch ($identity){
            case "province":
                $find = [
                    "province_id" => $data["province_id"],
                    "type" => 2
                ];
                break;
            case "city":
                $find = [
                    "province_id" => $data["province_id"],
                    "city_id" => $data["city_id"],
                    "type" => 3
                ];
                break;
            case "region":
                $find = [
                    "province_id" => $data["province_id"],
                    "city_id" => $data["city_id"],
                    "region_id" => $data["region_id"],
                    "type" => 4
                ];
                break;
            case "area":
                $find = [
                    "province_id" => $data["province_id"],
                    "city_id" => $data["city_id"],
                    "region_id" => $data["region_id"],
                    "area_id" => $data["area_id"],
                    "type" => 5
                ];
                break;
        }
        if($find = $this->where($find)->where("is_invalidation",0)->find()){
            if($find['user_id'] == $data['user_id']){
                return false;
            }
            return true;
        }
        return false;
    }
}