<?php
namespace app\store\controller\agent;

use app\common\exception\BaseException;
use app\store\controller\Controller;
use app\store\model\AgentWithdraw as AgentWithdrawModel;
use app\store\model\store\User;
use think\Session;

class Withdraw extends Controller{

    public function index($user_id = null, $apply_status = -1, $pay_type = -1, $search = ''){
        $model = new AgentWithdrawModel;
        return $this->fetch('index', [
            'list' => $model->getList($user_id, $apply_status, $pay_type, $search)
        ]);
    }

    /**
     * 提交提现申请
     * @param $data
     * @return array
     * @throws \app\common\exception\BaseException
     */
    public function apply()
    {
        if($this->request->isAjax()){
            $data = $this->postData('user');
            $model = new AgentWithdrawModel;
            $count = $model->where([
                'user_id' => Session::get('zuowey_store')['user']['store_user_id']
            ])->whereTime('create_time','today')->count();
            if($count >= 1){
                throw new BaseException(['code' => 0,'msg' => '一天只允许提现一次']);
            }
            $date = date("H");
            //提现时间限制
            if($date < 9 || $date >= 17){
                throw new BaseException(['msg' => '非正常提现时间无法提现，正常提现时间为：上午10点-下午5点']);
            }
            $user = User::detail(Session::get('zuowey_store')['user']['store_user_id']);
            if ($model->submit_apply($user, $data)) {
                return $this->renderSuccess('提现申请成功');
            }
            return $this->renderError($model->getError() ?: '提交失败');
        } else {
            return $this->fetch("apply",[
                'model' => User::detail(Session::get('zuowey_store')['user']['store_user_id'])
            ]);
        }
    }

    /**
     * 提现审核
     * @param $id
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function submit($id)
    {
        $model = AgentWithdrawModel::detail($id);
        if ($model->submit($this->postData('withdraw'))) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失败');
    }

    /**
     * 确认打款
     * @param $id
     * @return array
     * @throws \think\exception\DbException
     */
    public function money($id)
    {
        $model = AgentWithdrawModel::detail($id);
        if ($model->money()) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失败');
    }

    /**
     * 分销商提现：微信支付企业付款
     * @param $id
     * @return array|bool
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function wechat_pay($id)
    {
        $model = AgentWithdrawModel::detail($id);
        if ($model->wechatPay()) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失败');
    }
}