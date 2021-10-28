<?php
namespace app\store\controller\agent;

use app\common\model\Region;
use app\store\controller\Controller;
use app\store\model\store\AgentApply as AgentModel;
use app\store\model\store\User;
use think\Session;

class Agent extends Controller{

    public function apply($search = ''){
        $model = new AgentModel;
        $list = $model->getList($search);
        foreach ($list as &$value){
            $value['province'] = Region::getNameById($value['agent']['province_id']);
            $value['city'] = Region::getNameById($value['agent']['city_id']);
            $value['region'] = $value['agent']["region_id"] !== 0 ? Region::getNameById($value['agent']['region_id']) : '';
        }
        return $this->fetch('index', [
            'list' => $list,
            'store_user' => (new User())->where("store_user_id",Session::get("zuowey_store")['user']['store_user_id'])->find()
        ]);
    }

    /**
     * 确认用户转账
     * @param $apply_id
     */
    public function confirm_transfer($apply_id){
        $model = new AgentModel();
        if($model->where("apply_id",$apply_id)->update([
            'pay_status' => 20,
            'transfer_remark' => '用户转账打款成功'
        ])){
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失败');
    }

    public function submit(){
        $model = AgentModel::detail(input('apply_id'));
        if ($model->submit($this->postData('apply'))) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失败');
    }
}