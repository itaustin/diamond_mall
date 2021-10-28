<?php
namespace app\store\model;

use app\common\model\AgentBonusRecord;
use app\common\model\BaseModel;
use app\common\model\Setting;
use app\store\model\store\AgentApply;
use think\Queue;
use think\Session;
use app\store\model\store\User as StoreUserModel;

class Agent extends BaseModel{
    protected $agentWithModel;
    protected $agentApplyModel;
    protected $agentBonus;
    protected $order;
    protected $storeUserId;
    protected $storeUserModel;

    /**
     * 初始化Model层
     */
    protected function initialize()
    {
        parent::initialize();
        $this->agentApplyModel = new AgentApply();
        $this->agentWithModel = new AgentWith();
        $this->agentBonus = new AgentBonusRecord();
        $this->order = new Order();
        $this->storeUserId = Session::get('zuowey_store')['user']['store_user_id'];
        $this->storeUserModel = new StoreUserModel();
    }

    public function getTotalData(){
        //关于我的订单
        $data = [
            'widget-card' => [
                // 关于我的订单数
                'order_total' => $this->getOrderWithMe(),
                // 总分润金额
                'user_total' => $this->getBonusMoney(),
                // 今日分润金额
                'today_user_total' => $this->getTodayUserTotal(),
                // 金额冻结状态
                'frozen_status' => $this->getFrozenStatus(),
                // 冻结金额
                'frozen_money' => $this->getFrozenMoney()
            ]
        ];
        return $data;
    }

    protected function getOrderWithMe(){
        $bonus_record = $this->agentBonus->where("user_id",$this->storeUserId)->select();
        $order_ids = "";
        foreach ($bonus_record as $key => $value){
            $order_ids .= $value['order_id'].",";
        }
        $order_ids = substr($order_ids,0,strlen($order_ids)-1);;
        $orderModel = new Order();
        return $orderModel->where("pay_status",20)->where("order_id","in",$order_ids)->count();
    }

    protected function getBonusMoney(){
        return $this->agentBonus->where("user_id", $this->storeUserId)->sum('bonus_money');
    }

    protected function getTodayUserTotal(){
        return $this->agentBonus->whereTime("create_time", "today")->where("user_id", $this->storeUserId)->sum('bonus_money');
    }

    protected function getFrozenStatus(){
        $apply_money = $this->agentApplyModel->with(['agent'])->where("user_id", $this->storeUserId)->find();
        if($apply_money['agent']['pay_price'] > 0){
            return true;
        }
        return false;
    }

    protected function getFrozenMoney(){
        $apply_money = $this
            ->agentApplyModel
            ->alias('apply')
            ->join("agent_with","agent_with.agent_with_id = apply.agent_with_id")
            ->where("apply.user_id", $this->storeUserId)
            ->where('apply.apply_status', 20)
            ->where('apply.pay_status', 20)
            ->where('agent_with.pay_price','>',0)
            ->select();
        $money = 0;
        foreach($apply_money as $value){
            $money += $value['pay_price'];
        }
        return $money;
    }

    /**
     * 解冻条件
     */
    public function checkCondition(){
        $apply = $this->agentApplyModel->with(['agent'])->where("user_id", $this->storeUserId)->find();
        $setting = Setting::getAll('10001')['agent']['values'];
        // 获取订单数量
        $order_count = $this->getOrderWithMe();
        $status = false;
        if($apply['agent']['type'] == 5){
            // 小区代理
            if($order_count >= $setting['area']['task']){
                $status = true;
            }
        } else if($apply['agent']['type'] == 4){
            // 区代理
            if($order_count >= $setting['region']['task']){
                $status = true;
            }
        } else if($apply['agent']['type'] == 3){
            // 市代理
            if($order_count >= $setting['city']['task']){
                $status = true;
            }
        }else if($apply['agent']['type'] == 2){
            // 省代理
            if($order_count >= $setting['province']['task']){
                $status = true;
            }
        }
        return $status;
    }

    public function checkFreezeCondition(){
        $apply = $this->agentApplyModel->with(['agent'])->where("user_id", $this->storeUserId)->find();
        $status = false;
        if($apply['agent']['type'] == 5){
            // 小区代理
            if($apply['agent']['pay_price'] == 0){
                $status = $apply['agent']['type'];
            } else {
                $this->error = "该账户未解冻，无法冻结";
            }
        } else if($apply['agent']['type'] == 4){
            // 区代理
            if($apply['agent']['pay_price'] == 0){
                $status = $apply['agent']['type'];
            } else {
                $this->error = "该账户未解冻，无法冻结";
            }
        } else if($apply['agent']['type'] == 3){
            // 市代理
            if($apply['agent']['pay_price'] == 0){
                $status = $apply['agent']['type'];
            } else {
                $this->error = "该账户未解冻，无法冻结";
            }
        }else if($apply['agent']['type'] == 2){
            // 省代理
            if($apply['agent']['pay_price'] == 0){
                $status = $apply['agent']['type'];
            } else {
                $this->error = "该账户未解冻，无法冻结";
            }
        }
        return $status;
    }

    public function thaw($apply_id){
        $apply = $this->agentApplyModel
            ->where('apply_id',$apply_id)
            ->with(['agent'])
            ->where("user_id", $this->storeUserId)
            ->find();
        $money = $apply["agent"]['pay_price'];
        if($money !== 0){
            $this->storeUserModel->where("store_user_id",$this->storeUserId)->setInc("money",$money);
            $this->agentWithModel->where("agent_with_id",$apply['agent']['agent_with_id'])->setDec("pay_price",$money);
            $jobHandlerClassName = 'app\store\job\agent_money';
            $jobQueueName = "buyAgent";
            $jobData = ['ts' => time(), 'orderParam' => ["agent_with_id" => $apply['agent']['agent_with_id']], 'a' => 1];
            Queue::later( 172800,$jobHandlerClassName, $jobData, $jobQueueName);
            return true;
        }
        return false;
    }

}