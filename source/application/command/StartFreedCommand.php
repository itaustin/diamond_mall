<?php

namespace app\command;

use app\api\model\User;
use app\common\exception\BaseException;
use app\common\model\PointsCaptial;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class StartFreedCommand extends Command
{
    protected $output = null;

    protected function configure()
    {
        $this->setName("freed_points")->setDescription("总体释放积分");
    }

    protected function execute(Input $input, Output $output)
    {
        $this->output = $output;
        $this->freed();
    }

    public function freed() {
        $model = new PointsCaptial();
        $userModel = new User();
        $todayData = $model
            ->whereTime("create_time", "today")
            ->where("type", "not in","10,20,60")
//            ->where("user_id", "in", "10019")
            ->where("is_delete", 0)
            ->select();
        foreach ($todayData as $value) {
            $info = $userModel->where("user_id", $value["user_id"])->find();
            $userModel->startTrans();
            try {
                if($info["points"] >= $value["points"]) {
                    $userModel->where("user_id", $value["user_id"])
                        ->setDec("points", $value["points"]);
                    $userModel->where("user_id", $value["user_id"])
                        ->setInc("freeze_points", $value["points"]);
                    echo "扣除" . $value["points"] . "，现在积分是" . $info["points"] . "\r\n";
                } else {
                    if($info["points"] !== 0) {
                        $userModel->where("user_id", $value["user_id"])
                            ->setDec("points", $info["points"]);
                        $userModel->where("user_id", $value["user_id"])
                            ->setInc("freeze_points", $info["points"]);
                        echo "扣除" . $value["points"] . "，现在积分是" . $info["points"] . "\r\n";
                    }
                }
                $userModel->commit();
            } catch (BaseException $exception) {
                echo $exception->getMessage() . "\r\n";
            }
        }
        return true;
    }
}