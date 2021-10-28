<?php
namespace app\store\model\store;

use app\common\model\store\AgentApply as AgentModel;
use app\store\model\store\User as StoreUserModel;
use think\Session;

class AgentApply extends AgentModel{

    /**
     * 获取省市区分销商列表
     * @param string $search
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getList($search = '')
    {
        // 构建查询规则
        $this->alias('apply')
            ->field('apply.*, store_user.real_name, store_user.user_name')
            ->join('store_user', 'store_user.store_user_id = apply.user_id')
            ->with(['agent'])
            ->order(['apply.create_time' => 'desc']);
        // 查询条件
        !empty($search) && $this->where('store_user.real_name', 'like', "%$search%");
        $session = Session::get('zuowey_store')['user']['store_user_id'];
        $storeModel = new StoreUserModel();
        $storeInfo = $storeModel->where("store_user_id",$session)->find();
        if($storeInfo['store_user_id'] !== 10002 && $storeInfo['is_super'] !== 1){
            $this->where("store_user_id",$session);
        }
        // 获取列表数据
        return $this->paginate(15, false, [
            'query' => \request()->request()
        ]);
    }

    /**
     * 确认审核省市区代理
     * @param $data
     * @return bool
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function submit($data)
    {
        if ($data['apply_status'] == '30' && empty($data['reject_reason'])) {
            $this->error = '请填写驳回原因';
            return false;
        }
        // 更新申请记录
        $data['audit_time'] = time();
        $status = $this->allowField(true)->save($data);
        if($status){

        }
        return true;
    }

}