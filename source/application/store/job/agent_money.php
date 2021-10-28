<?php

namespace app\store\job;

use app\store\model\AgentWith;
use app\store\model\store\AgentApply;
use think\queue\Job;

class agent_money {
    public function fire(Job $job, $data)
    {
        $isJobStillNeedToBeDone = $this->checkDatabaseToSeeIfJobNeedToBeDone($data);
        if (!$isJobStillNeedToBeDone) {
            $job->delete();
        }
        $isJobDone = $this->doAgentApply($data);
        if ($isJobDone) {
            //如果任务执行成功， 记得删除任务
            $job->delete();
        } else {
            if ($job->attempts() > 3) {
                //通过这个方法可以检查这个任务已经重试了几次了
                $job->delete();
                // 也可以重新发布这个任务
                //print("<info>Hello Job will be availabe again after 2s."."</info>\n");
//                $job->release(2); //$delay为延迟时间，表示该任务延迟2秒后再执行
            }
        }
    }

    private function checkDatabaseToSeeIfJobNeedToBeDone($data)
    {
        return true;
    }

    private function doAgentApply($data) {
        $agent_with_id = $data['orderParam']['agent_with_id'];
        $model = new AgentWith();
        $applyModel = new AgentApply();
        $data = $model->where("agent_with_id",$agent_with_id)->with(['agent'])->find();
        if($data["pay_price"] == 0){
            if($model->where("agent_with_id",$data["agent_with_id"])->update([
                'is_invalidation' => 1
            ])){
                echo "删除账户状态\r\n------------\r\n";
            }
        }
    }
}