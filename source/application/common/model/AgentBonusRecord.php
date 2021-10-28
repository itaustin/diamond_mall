<?php
namespace app\common\model;

use app\store\model\store\User as StoreUserModel;
use app\store\model\AgentWith as AgentWithModel;
use think\Session;

class AgentBonusRecord extends BaseModel{
    protected $name = "agent_bonus";

    public function getList(){
        // 构建查询规则
        $this->alias('record')
            ->join('store_user', 'store_user.store_user_id = record.user_id')
            ->with(['user','with_order'])
            ->field('record.*,store_user.user_name,store_user.real_name')
            ->order(['record.create_time' => 'desc']);
//        // 查询条件
        !empty($search) && $this->where('store_user.real_name', 'like', "%$search%");
        $session = Session::get('zuowey_store')['user']['store_user_id'];
        $storeModel = new StoreUserModel();
        $storeInfo = $storeModel->where("store_user_id",$session)->find();
        if($storeInfo['store_user_id'] !== 10002 && $storeInfo['is_super'] !== 1){
            $this->where("user_id",$session);
        }
        // 获取列表数据
        return $this->paginate(15, false, [
            'query' => \request()->request()
        ]);
    }

    public function getRelationList(){
        $withMeRegionOrderIds = $this->getRecordOrderId();
        // 构建查询规则
        $this->alias('record')
            ->join('store_user', 'store_user.store_user_id = record.user_id')
            ->with(['user','with_order'])
            ->field('record.*,store_user.user_name,store_user.real_name')
            ->order(['record.create_time' => 'desc']);
        !empty($withMeRegionOrderIds) && $this->where("order_id","in",$withMeRegionOrderIds);
        !empty($search) && $this->where('store_user.real_name', 'like', "%$search%");
        // 获取列表数据
        return $this->paginate(15, false, [
            'query' => \request()->request()
        ]);
    }

    public function user(){
        return $this->hasOne("app\common\model\store\User","store_user_id","user_id");
    }

    public function withOrder(){
        return $this->hasOne('app\api\model\Order', "order_id","order_id")->with(['user','goods','goods.image']);
    }

    public function getRecordOrderId(){
        $model = new AgentWithModel;
        $store_user_id = Session::get("zuowey_store")['user']['store_user_id'];
        $data = $model->where("user_id",$store_user_id)->select();
        $provinceIds = "";
        $cityIds = "";
        $regionIds = "";
        foreach ($data as $value){
            $provinceIds .= $value["province_id"].",";
            $cityIds .= $value["city_id"] . ",";
            $regionIds .= $value["region_id"] . ",";
        }
        $model = new OrderAddress();
        $data = $model
            ->whereOr("province_id",$provinceIds)
            ->whereOr("city_id",$cityIds)
            ->whereOr("region_id",$regionIds)
            ->select();
        $orderIds = "";
        foreach ($data as $value){
            $orderIds .= $value["order_id"].",";
        }
        return $orderIds;
    }
}