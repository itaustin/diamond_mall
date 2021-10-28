<?php
namespace app\store\controller\agent;

use app\store\controller\Controller;
use app\common\model\AgentBonusRecord as AgentBonusRecordModel;

class Record extends Controller{
    public function index(){
        $model = new AgentBonusRecordModel();
        return $this->fetch('index',[
            'list' => $model->getList()
        ]);
    }

    public function relation(){
        $model = new AgentBonusRecordModel();
        return $this->fetch('relation', [
            'list' => $model->getRelationList()
        ]);
    }
}