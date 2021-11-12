<?php

namespace app\command;

use app\api\model\UserReferee;
use app\common\model\User;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class PointsCommand extends Command
{
    protected function configure()
    {
        $this->setName("points")->setDescription("每日释放积分");
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('任务开始');
        $this->bonus();
        echo "\r\n";
    }

    public function bonus(){
        echo "开始释放积分到freeze_points";
        $model = new User();
        $allUser = $model->select();
        // 首次释放0.003
        foreach ($allUser as $k => $value){
            // 每日释放千分之3
            $thousand = bcmul($value["points"], 0.003, 2);
            $model->where("user_id", $value["user_id"])
                ->setDec("points", $thousand);
            $model->where("user_id", $value["user_id"])
                ->setInc("freeze_points", $thousand);
            $GLOBALS['all_user'] = [];
            $this->getFirst($value['user_id']);
            // 查找出自己的所有团队人员
            // 计算团队等级
            // 达到级别给到加速积分
            dump($GLOBALS['all_user']);

            // 查找上级一代，找到给积分加速释放

            // 查找下级一代，找到给积分加速释放

            // 查找下级二代，找到给积分加速释放
        }

    }

    public function getFirst($user_id)
    {
        $model = new UserReferee();
        $data = $model
            ->where("dealer_id", $user_id)
            ->where("level", 1)
            ->field("id,dealer_id, user_id")
            ->select();
        foreach ($data as $value) {
            $GLOBALS['all_user'][] = $value['users']->toArray();
            $this->getFirst($value["user_id"]);
        }
    }
}