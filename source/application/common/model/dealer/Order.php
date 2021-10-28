<?php

namespace app\common\model\dealer;

use app\api\model\Goods;
use app\api\model\OrderGoods;
use think\Hook;
use app\common\model\BaseModel;
use app\common\model\Setting as Setting;
use app\common\model\dealer\Setting as SettingModel;
use app\common\enum\OrderType as OrderTypeEnum;

/**
 * 分销商订单模型
 * Class Apply
 * @package app\common\model\dealer
 */
class Order extends BaseModel
{
    protected $name = 'dealer_order';

    /**
     * 订单模型初始化
     */
    public static function init()
    {
        parent::init();
        // 监听分销商订单行为管理
        $static = new static;
        Hook::listen('DealerOrder', $static);
    }

    /**
     * 订单所属用户
     * @return \think\model\relation\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('app\common\model\User');
    }

    /**
     * 一级分销商用户
     * @return \think\model\relation\BelongsTo
     */
    public function dealerFirst()
    {
        return $this->belongsTo('User', 'first_user_id');
    }

    /**
     * 二级分销商用户
     * @return \think\model\relation\BelongsTo
     */
    public function dealerSecond()
    {
        return $this->belongsTo('User', 'second_user_id');
    }

    /**
     * 三级分销商用户
     * @return \think\model\relation\BelongsTo
     */
    public function dealerThird()
    {
        return $this->belongsTo('User', 'third_user_id');
    }

    /**
     * 订单类型
     * @param $value
     * @return array
     */
    public function getOrderTypeAttr($value)
    {
        $types = OrderTypeEnum::getTypeName();
        return ['text' => $types[$value], 'value' => $value];
    }

    /**
     * 订单详情
     * @param $where
     * @return Order|null
     * @throws \think\exception\DbException
     */
    public static function detail($where)
    {
        return static::get($where);
    }

    /**
     * 订单详情
     * @param $orderId
     * @param $orderType
     * @return Order|null
     * @throws \think\exception\DbException
     */
    public static function getDetailByOrderId($orderId, $orderType)
    {
        return static::detail(['order_id' => $orderId, 'order_type' => $orderType]);
    }

    /**
     * 发放分销订单佣金
     * @param array|\think\Model $order 订单详情
     * @param int $orderType 订单类型
     * @return bool|false|int
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public static function grantMoney($order, $orderType = OrderTypeEnum::MASTER)
    {
        // 订单是否已完成
        // 关闭订单完成
        //if ($order['order_status']['value'] != 30) {
        //    return false;
        //}
        // 佣金结算天数
        $settleDays = SettingModel::getItem('settlement', $order['wxapp_id'])['settle_days'];
        // 判断该订单是否满足结算时间 (订单完成时间 + 佣金结算时间) ≤ 当前时间
        $deadlineTime = $order['receipt_time'] + ((int)$settleDays * 86400);
        if ($settleDays > 0 && $deadlineTime > time()) {
            return false;
        }
        // 分销订单详情
        $model = self::getDetailByOrderId($order['order_id'], $orderType);
        if (!$model || $model['is_settled'] == 1) {
            return false;
        }
        // 重新计算分销佣金
        $capital = $model->getCapitalByOrder($order);
        $first_points = bcmul($capital['first_money'],0.05,2);
        $second_points = bcmul($capital['second_money'],0.05,2);
        $third_points = bcmul($capital['third_money'],0.05,2);
        // 发放一级分销商佣金
        $model['first_user_id'] > 0 && User::grantMoney($model['first_user_id'], $capital['first_money']);
        //$first_points > 0 && User::grantPoints($model['first_user_id'],$first_points);
        // 发放二级分销商佣金
        $model['second_user_id'] > 0 && User::grantMoney($model['second_user_id'], $capital['second_money']);
        //$second_points > 0 && User::grantPoints($model['second_user_id'],$second_points);
        // 发放三级分销商佣金
        $model['third_user_id'] > 0 && User::grantMoney($model['third_user_id'], $capital['third_money']);
        //$third_points > 0 && User::grantPoints($model['third_user_id'],$third_points);
        // 发放团队佣金
        $team = json_decode($model['team_money_resource'],true);
        if(!empty($team)){
            foreach ($team as $key => $value){
                //不分积分
                $value['money'] > 0 && User::grantMoney($value['dealer_id'],bcsub($value['money'], 0,2));
//                $value['money'] > 0 && User::grantMoney($value['dealer_id'],bcsub($value['money'], bcmul($value['money'],0.05,2),2));
//                $value['money'] > 0 && User::grantPoints($value['dealer_id'],bcmul($value['money'],0.05,2));
            }
        }
        // 更新分销订单记录
        return $model->save([
            'order_price' => $capital['orderPrice'],
            'first_money' => $capital['first_money'],
            'second_money' => $capital['second_money'],
            'third_money' => $capital['third_money'],
            'is_settled' => 1,
            'settle_time' => time()
        ]);
    }

    /**
     * 计算订单分销佣金
     * @param $order
     * @return array
     */
    protected function getCapitalByOrder($order)
    {
        // 分销佣金设置
        $setting = SettingModel::getItem('commission', $order['wxapp_id']);
        // 分销层级
        $level = SettingModel::getItem('basic', $order['wxapp_id'])['level'];
        // 分销订单佣金数据
        $capital = [
            // 订单总金额(不含运费)
            'orderPrice' => bcsub($order['pay_price'], $order['express_price'], 2),
            // 一级分销佣金
            'first_money' => 0.00,
            // 二级分销佣金
            'second_money' => 0.00,
            // 三级分销佣金
            'third_money' => 0.00
        ];
        // 计算分销佣金
        foreach ($order['goods'] as $goods) {
            // 判断商品存在售后退款则不计算佣金
            if ($this->checkGoodsRefund($goods)) {
                continue;
            }
            // 商品实付款金额
            $goodsPrice = min($capital['orderPrice'], $goods['total_pay_price']);
            // 计算商品实际佣金
            $goodsCapital = $this->calculateGoodsCapital($setting, $goods, $goodsPrice);
            // 累积分销佣金
            $level >= 1 && $capital['first_money'] += $goodsCapital['first_money'];
            $level >= 2 && $capital['second_money'] += $goodsCapital['second_money'];
            $level == 3 && $capital['third_money'] += $goodsCapital['third_money'];
        }
        return $capital;
    }

    /**
     *
     */
    protected function getTeamCapitalByOrder($order){
        $goods_id = (new OrderGoods())->where("order_id",$order['order_id'])->field("goods_id")->find()['goods_id'];
        $mall_no = (new Goods())->where("goods_id",$goods_id)->field('mall_no')->find()['mall_no'];
        //分销团队设置
        $setting = Setting::getItem('teamcommission', $order['wxapp_id'])[$mall_no];
        $bonusMoney = $setting['first_team']['level_four']['money'];
        $parentId = $order['user_id'];
        $meetMoney = [];
        $tempMoney = [];
        $accumulation = [];
        while($bonusMoney){
            if($parentId == 0){
                $bonusMoney = false;
            }
            //查找到上级
            $parentDealerId = (new Referee())
                ->where('user_id',$parentId)
                ->where('level','1')->find()['dealer_id'];
//            $checkIsHasPeople = new \app\api\model\User();
            //检查是否有效客户，不是有效客户循环跳过。
//            if(!$checkIsHasPeople->where('user_id',$parentDealerId)->where('pay_money','>',0)->find()){
//                $parentId = $parentDealerId;
//                continue;
//            }
            //获取到该上级该得的钱
            $bonusData = $this->checkVerifyTeamCapital($parentDealerId,$setting);
            if($bonusData === null){
                $parentId = $parentDealerId;
                continue;
            }
            if(count($tempMoney) > 0){
                if($tempMoney['money'] < $bonusData["money"]){
                    if($bonusMoney < $setting['first_team']['level_one']['money']){
                        if(User::isDealerUser($parentDealerId)){
                            if(isset($accumulation['last']) && $accumulation['last'] == true){
                                $bonusData['money'] = $accumulation['money'];
                                array_push($meetMoney,$bonusData);
                            }else{
                                $bonusData['money'] = $bonusMoney;
                                array_push($meetMoney,$bonusData);
                                $bonusMoney -= $bonusData['money'];
                            }
                        }else{
                            $bonusData['money'] = $bonusMoney;
                            $accumulation = $bonusData;
                            $accumulation['last'] = true;
                        }
                    }else{
                        if(User::isDealerUser($parentDealerId)) {
                            $bonusData['money'] = $bonusData['money'] - $tempMoney['money'];
                            array_push($meetMoney, $bonusData);
                            $tempMoney = $bonusData;
                            $bonusMoney -= $bonusData['money'];
                            $parentId = $parentDealerId;
                            continue;
                        }else{
                            if($bonusData['money'] > $accumulation['money']){
                                $accumulation = $bonusData;
                            }
                        }
                    }
                }
//                    else if($tempMoney["money"] == $bonusData["money"]){
//                        if($bonusMoney < $setting['first_team']['level_one']['money']){
//                            $bonusData['money'] = $bonusMoney;
//                            array_push($meetMoney,$bonusData);
//                            $bonusMoney -= $bonusData['money'];
//                        }
//                    }
            }
            else{
                /**
                 * 分红金额等于最大分配佣金时，自身全部拿走
                 */
                if($bonusData['money'] == $bonusMoney){
                    if(User::isDealerUser($parentDealerId)){
                        array_push($meetMoney,$bonusData);
                        $tempMoney = $bonusData;
                        $bonusMoney = $bonusMoney - $bonusData['money'];
                        $bonusMoney = false;
                        break;
                    }else{
                        $accumulation = $bonusData;
                    }
                }else if($bonusData['money'] < $bonusMoney){
                    /**
                     * 分红金额小于最大分红金额时，自身拿走满足自己条件的金额
                     */
                    if(User::isDealerUser($parentDealerId)){
                        array_push($meetMoney,$bonusData);
                        $tempMoney = $bonusData;
                        $bonusMoney = $bonusMoney - $bonusData['money'];
                    }else{
                        $accumulation = $bonusData;
                    }
                }else if($bonusData['money'] > $bonusMoney){
                    /**
                     * 分红金额大于最大分红金额时，自身拿走满足自己条件的金额
                     */
                    if(User::isDealerUser($parentDealerId)){
                        $tempMoney = $bonusData;
                        $bonusData['money'] = $bonusMoney;
                        array_push($meetMoney,$bonusData);
                        $bonusMoney = $bonusMoney - $bonusMoney;
                    }else{
                        $accumulation = $bonusData;
                    }
                }
            }

            $parentId = $parentDealerId;

            if($bonusMoney <= 0){
                $bonusMoney = false;
            }
        }
        return $meetMoney;
    }

    private function checkVerifyTeamCapital($dealer_id,$setting){
        /**
         * 备注：
         * 比如团队满足条件一：拿30元，满足条件二：拿40元，50-同上，60-同上
         * 找到一个满足30的，拿走30
         * 找到一个满足40的，他自己需要拿40，结果被上边拿走了30，他自己就拿10块 | 如果没有找到30的，第一个就找到了40，然后第二个找到30的时候，30元的那个人就不拿钱了
         * 找到一个满足50的  同上
         * 找到一个满足60的  同上
         * 如果没有找到满足60的，剩余10块回平台
         */
        $first = (new Referee())->alias('r')->where([
            'dealer_id' => $dealer_id,
            "level" => '1'
        ])->join('user u','r.user_id = u.user_id')->where('u.pay_money','>',0)->count();
        $second = (new Referee())->alias('r')->where([
            'dealer_id' => $dealer_id,
            "level" => '2'
        ])->join('user u','r.user_id = u.user_id')->where('u.pay_money','>',0)->count();
        $newStateArr = null;
        $dealer_name = \app\common\model\User::detail($dealer_id)['nickName'];
//        $dealer_name = User::detail($dealer_id)['real_name'];
        if($first >= $setting['first_team']['level_one']['first'] && ($first+$second) >= $setting['first_team']['level_one']['second']){
            $newStateArr = [
                'dealer_id' => $dealer_id,
                'level' => 'level_one',
                'money' => $setting['first_team']['level_one']['money'],
                'real_name' => $dealer_name
            ];
        }
        if($first >= $setting['first_team']['level_two']['first'] && ($first+$second) >= $setting['first_team']['level_two']['second']){
            $newStateArr = [
                'dealer_id' => $dealer_id,
                'level' => 'level_two',
                'money' => $setting['first_team']['level_two']['money'],
                'real_name' => $dealer_name];
        }
        if($first >= $setting['first_team']['level_three']['first'] && ($first+$second) >= $setting['first_team']['level_three']['second']){
            $newStateArr = [
                'dealer_id' => $dealer_id,
                'level' => 'level_three',
                'money' => $setting['first_team']['level_three']['money'],
                'real_name' => $dealer_name];
        }
        if($first >= $setting['first_team']['level_four']['first'] && ($first+$second) >= $setting['first_team']['level_four']['second']){
            $newStateArr = [
                'dealer_id' => $dealer_id,
                'level' => 'level_four',
                'money' => $setting['first_team']['level_four']['money'],
                'real_name' => $dealer_name];
        }

        return $newStateArr;
    }

    /**
     * 计算商品实际佣金
     * @param $setting
     * @param $goods
     * @param $goodsPrice
     * @return array
     */
    private function calculateGoodsCapital($setting, $goods, $goodsPrice)
    {
        // 判断是否开启商品单独分销
        if ($goods['is_ind_dealer'] == false) {
            // 全局分销比例
            return [
                'first_money' => $goodsPrice * ($setting['first_money'] * 0.01),
                'second_money' => $goodsPrice * ($setting['second_money'] * 0.01),
                'third_money' => $goodsPrice * ($setting['third_money'] * 0.01)
            ];
        }
        // 商品单独分销
        if ($goods['dealer_money_type'] == 10) {
            // 分销佣金类型：百分比
            return [
                'first_money' => $goodsPrice * ($goods['first_money'] * 0.01),
                'second_money' => $goodsPrice * ($goods['second_money'] * 0.01),
                'third_money' => $goodsPrice * ($goods['third_money'] * 0.01)
            ];
        } else {
            return [
                'first_money' => $goods['total_num'] * $goods['first_money'],
                'second_money' => $goods['total_num'] * $goods['second_money'],
                'third_money' => $goods['total_num'] * $goods['third_money']
            ];
        }
    }

    /**
     * 验证商品是否存在售后
     * @param $goods
     * @return bool
     */
    private function checkGoodsRefund($goods)
    {
        return !empty($goods['refund'])
            && $goods['refund']['type']['value'] == 10
            && $goods['refund']['is_agree']['value'] != 20;
    }

}
