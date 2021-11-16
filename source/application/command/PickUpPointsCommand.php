<?php

namespace app\command;

use app\api\model\Goods;
use app\api\model\Order;
use app\api\model\UserReferee;
use app\common\exception\BaseException;
use app\common\model\PointsCaptial;
use app\common\model\User;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class PickUpPointsCommand extends Command
{
    protected $output = null;

    protected function configure()
    {
        $this->setName("pickup_points")->setDescription("每日释放积分");
    }

    protected function execute(Input $input, Output $output)
    {
        $this->output = $output;
        $output->writeln('任务开始');
        $this->bonus();
    }

    public function bonus(){
        $model = new User();
        $pointsCapitalModel = new PointsCaptial();
        $refereeModel = new UserReferee();
        $allUser = $model
            ->where("points",">", 0)
            ->select();
        // 上拿一代
        $model->startTrans();
        try {
            $dataInfo = [];
            foreach ($allUser as $value) {
                $inviteUser = $refereeModel
                    ->where("user_id", $value["user_id"])
                    ->where("level", 1)
                    ->find()["dealer_id"];
                $info = $model->where("user_id", $inviteUser)
                    ->find();
                $formatInvite = bcmul($info["points"], 0.003, 2);
                // 开始查询上拿一代是否有报单
                if($inviteUser) {
                    $parentStatus = $this->checkUserStatus($inviteUser);
                    if($parentStatus){
                        $this->output->writeln($value["user_id"] . "的邀请人是：" . $inviteUser);
                        $percent20 = bcmul($formatInvite, 0.2, 2);
                        $dataInfo[] = [
                            "points" => $percent20,
                            "user_id" => $value["user_id"]
                        ];
//                        $model->where("user_id", $value["user_id"])->setInc("freeze_points", $percent20);
//                        $model->where("user_id", $value["user_id"])->setDec("points", $percent20);
                        $pointsCapitalModel->insert([
                            "order_id" => 0,
                            "type" => 40,
                            "user_id" => $value["user_id"],
                            "points" => $percent20,
                            "description" => "上拿一代{$info['username']}的加速释放",
                            "consignment_money" => 0,
                            "is_delete" => 0,
                            "wxapp_id" => 10001,
                            "create_time" => time()
                        ]);
                    }
                }
            }
            dump($dataInfo);
        } catch(BaseException $e) {
            $this->output->writeln($e->getMessage());
        }
    }

    /**
     * 检测用户是否购买且支付过报单商品
     * @param $user_id
     */
    public function checkUserStatus($user_id){
        $model = new Order();
        $goodsModel = new Goods();
        $data = $model->where("user_id", $user_id)
            ->where("pay_status", 20)
            ->select();
        $isHaveMemberMallGoods = false;
        foreach($data as $value) {
            $goods_id = $value["goods"][0]["goods_id"];
            $goodsInfo = $goodsModel->where("goods_id", $goods_id)
                ->find();
            if($goodsInfo["category_id"] == 10001) {
                $isHaveMemberMallGoods = true;
            }
        }
        return $isHaveMemberMallGoods;
    }
}