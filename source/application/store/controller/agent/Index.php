<?php
namespace app\store\controller\agent;

use app\common\model\Region;
use app\store\controller\Controller;
use app\store\model\AgentWith;
use app\store\model\Setting as SettingModel;
use app\store\model\Store as StoreModel;
use app\store\model\Agent as AgentModel;

class Index extends Controller{

    public function index(){
        $model = new AgentModel();
        $applyData = (new AgentWith())
            ->alias('with')
            ->join("agent_apply apply", 'apply.agent_with_id = with.agent_with_id')
            ->where('apply.apply_status', 20)
            ->where('apply.pay_status', 20)
            ->where('with.pay_price','>',0)
            ->where("with.user_id",$this->store['user']['store_user_id'])
            ->select();
        foreach ($applyData as &$value){
            $value['province'] = Region::getNameById($value['province_id']);
            $value['city'] = Region::getNameById($value['city_id']);
            $value['region'] = $value["region_id"] !== 0 ? Region::getNameById($value['region_id']) : '';
        }
        return $this->fetch('index', [
            'data' => $model->getTotalData(),
            'apply' => $applyData
        ]);
    }

    public function thaw(){
        $apply_id = input('apply_id');
        if(empty($apply_id)){
            return $this->renderError("您还未选择要解冻的代理账户");
        }
        // 检测是否满足解冻条件
        $model = new AgentModel();
        $status = $model->checkCondition();
        if(!$status){
            return $this->renderError("不满足解冻条件，请稍候再试");
        }
        if($model->thaw($apply_id)){
            return $this->renderSuccess("解冻成功，解冻金额已打款至提现金额");
        }
        return $this->renderError("解冻失败");
    }

    public function freeze(){
        $apply_id = input('apply_id');
        if(empty($apply_id)){
            return $this->renderError("您还未选择要冻结的代理账户");
        }
        // 检测是否满足冻结条件
        $model = new AgentModel();
        if(!$status = $model->checkFreezeCondition()){
            return $this->renderError($model->getError());
        }
        $setting_data = SettingModel::getAll('10001')['agent']['values'];
        $data['user_id'] = $this->store['user']['store_user_id'];
        if($status == 2){
            $data['identity'] = "province";
        } else if($status == 3){
            $data['identity'] = "city";
        } else if($status == 4){
            $data['identity'] = "region";
        } else if($status == 5){
            $data['identity'] = "area";
        }
    }
}