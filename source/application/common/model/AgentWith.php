<?php
namespace app\common\model;

class AgentWith extends BaseModel
{

    public function agent(){
        return $this->hasOne('app\common\model\store\AgentApply');
    }
}