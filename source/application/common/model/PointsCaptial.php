<?php


namespace app\common\model;


class PointsCaptial extends BaseModel
{
    protected $autoWriteTimestamp = true;
    protected $createTime = "create_time";

    public function getList($user_id){
        $model = new static;
        return $model
            ->with(['user'])
            ->where("user_id", $user_id)
            ->select();
    }

    public function user(){
        return $this->hasOne("User","user_id","user_id");
    }
}