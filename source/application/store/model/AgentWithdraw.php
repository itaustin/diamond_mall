<?php
namespace app\store\model;

use app\api\model\dealer\Setting;
use app\common\exception\BaseException;
use app\common\model\AgentWithdraw as AgentWithdrawModel;
use app\common\service\Message;
use app\store\model\store\User as StoreUserModel;
use app\store\model\Wxapp as WxappModel;
use app\store\model\store\User;
use think\Session;

class AgentWithdraw extends AgentWithdrawModel{
    /**
     * 获取器：申请时间
     * @param $value
     * @return false|string
     */
    public function getAuditTimeAttr($value)
    {
        return $value > 0 ? date('Y-m-d H:i:s', $value) : 0;
    }

    /**
     * 获取器：打款方式
     * @param $value
     * @return mixed
     */
    public function getPayTypeAttr($value)
    {
        return ['text' => $this->payType[$value], 'value' => $value];
    }

    /**
     * 获取分销商提现列表
     * @param null $user_id
     * @param int $apply_status
     * @param int $pay_type
     * @param string $search
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getList($user_id = null, $apply_status = -1, $pay_type = -1, $search = '')
    {
        // 构建查询规则
        $this->alias('withdraw')
            ->with(['user'])
            ->field('withdraw.*, agent.real_name, agent.mobile_phone')
            ->join('store_user agent', 'agent.store_user_id = withdraw.user_id')
            ->order(['withdraw.create_time' => 'desc']);
        // 查询条件
        $user_id > 0 && $this->where('withdraw.user_id', '=', $user_id);
        !empty($search) && $this->where('agent.real_name|agent.mobile_phone', 'like', "%$search%");
        $apply_status > 0 && $this->where('withdraw.apply_status', '=', $apply_status);
        $pay_type > 0 && $this->where('withdraw.pay_type', '=', $pay_type);

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
     * 分销商提现审核
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
        $this->allowField(true)->save($data);
        // 提现驳回：解冻分销商资金
        $data['apply_status'] == '30' && User::backFreezeMoney($this['user_id'], $this['money']);
        // 发送模板消息
        (new Message)->withdraw($this);
        return true;
    }

    public function submit_apply($user,$data){
        // 数据验证
        $this->validation($user, $data);
        $data['money'] = $data['money'] - 3;
        $model = new User();
//        $userInfo = $model->where('user_id',$user['store_user_id'])->find();
//        if($userInfo['alipay_account'] == "" || $userInfo['alipay_name'] == ""){
//            $userInfo->allowField(true)->save([
//                'alipay_account' => $data['alipay_account'],
//                'alipay_name' => $data['alipay_name']
//            ]);
//        }
        // 新增申请记录
        $this->save(array_merge($data, [
            'user_id' => $user['store_user_id'],
            'apply_status' => 10,
            'wxapp_id' => self::$wxapp_id ?: 10001,
        ]));
        // 冻结用户资金
        $user->freezeMoney($data['money']);
        return true;
    }

    /**
     * 数据验证
     * @param $dealer
     * @param $data
     * @throws BaseException
     */
    private function validation($dealer, $data)
    {
        // 结算设置
        $settlement = Setting::getItem('settlement');
        // 最低提现佣金
        if ($data['money'] <= 0) {
            throw new BaseException(['msg' => '提现金额不正确']);
        }
        if ($dealer['money'] <= 0) {
            throw new BaseException(['msg' => '当前用户没有可提现佣金']);
        }
        if ($data['money'] > $dealer['money']) {
            throw new BaseException(['msg' => '提现金额不能大于可提现佣金']);
        }
        if ($data['money'] < $settlement['min_money']) {
            throw new BaseException(['msg' => '最低提现金额为' . $settlement['min_money']]);
        }
        if (!in_array($data['pay_type'], $settlement['pay_type'])) {
            throw new BaseException(['msg' => '提现方式不正确']);
        }
        if ($data['pay_type'] == '20') {
            if (empty($data['alipay_name']) || empty($data['alipay_account'])) {
                throw new BaseException(['msg' => '请补全提现信息']);
            }
        } elseif ($data['pay_type'] == '30') {
            if (empty($data['bank_name']) || empty($data['bank_account']) || empty($data['bank_card'])) {
                throw new BaseException(['msg' => '请补全提现信息']);
            }
        }
    }

    /**
     * 确认提现成功
     * @return bool
     * @throws \think\exception\PDOException
     */
    public function money()
    {
        $this->startTrans();
        try {
            // 更新申请状态
            $this->allowField(true)->save([
                'apply_status' => 40,
                'audit_time' => time(),
            ]);
            // 更新分销商累积提现佣金
            User::totalMoney($this['user_id'], $this['money']);
            // 记录分销商资金明细
            AgentCapital::add([
                'user_id' => $this['user_id'],
                'flow_type' => 20,
                'money' => -$this['money'],
                'describe' => '申请提现',
            ]);
            // 事务提交
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 分销商提现：微信支付企业付款
     * @return bool
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function wechatPay()
    {
        // 微信用户信息
        $user = $this['user']['user'];
        // 生成付款订单号
        $orderNO = OrderService::createOrderNo();
        // 付款描述
        $desc = '分销商提现付款';
        // 微信支付api：企业付款到零钱
        $wxConfig = WxappModel::getWxappCache();
        $WxPay = new WxPay($wxConfig);
        // 请求付款api
        if ($WxPay->transfers($orderNO, $user['open_id'], $this['money'], $desc)) {
            // 确认提现成功
            $this->money();
            return true;
        }
        return false;
    }
}