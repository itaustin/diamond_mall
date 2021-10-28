<?php

namespace app\api\controller\user\dealer;

use app\api\controller\Controller;
use app\api\model\dealer\Setting;
use app\api\model\dealer\User as DealerUserModel;
use app\api\model\dealer\Withdraw as WithdrawModel;
use app\common\exception\BaseException;

/**
 * 分销商提现
 * Class Withdraw
 * @package app\api\controller\user\dealer
 */
class Withdraw extends Controller
{
    /* @var \app\api\model\User $user */
    private $user;

    private $dealer;
    private $setting;

    /**
     * 构造方法
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function _initialize()
    {
        parent::_initialize();
        // 用户信息
        $this->user = $this->getUser();
        // 分销商用户信息
        $this->dealer = DealerUserModel::detail($this->user['user_id']);
        // 分销商设置
        $this->setting = Setting::getAll();
    }

    /**
     * 提交提现申请
     * @param $data
     * @return array
     * @throws \app\common\exception\BaseException
     */
    public function submit($data)
    {
        $formData = json_decode(htmlspecialchars_decode($data), true);
        $model = new WithdrawModel;
        $count = $model->where([
            'user_id' => $this->dealer['user_id']
        ])->whereTime('create_time','today')->count();
        if($count >= 1){
            throw new BaseException(['code' => 0,'msg' => '一天只允许提现一次']);
        }
        $date = date("H");
        //提现时间限制
        if($date < 9 || $date >= 17){
            throw new BaseException(['msg' => '非正常提现时间无法提现，正常提现时间为：上午10点-下午5点']);
        }
        if ($model->submit($this->dealer, $formData)) {
            return $this->renderSuccess([], '申请提现成功');
        }
        return $this->renderError($model->getError() ?: '提交失败');
    }

    /**
     * 分销商提现明细
     * @param int $status
     * @return array
     * @throws \think\exception\DbException
     */
    public function lists($status = -1)
    {
        $model = new WithdrawModel;
        return $this->renderSuccess([
            // 提现明细列表
            'list' => $model->getList($this->user['user_id'], (int)$status),
            // 页面文字
            'words' => $this->setting['words']['values'],
        ]);
    }

}
