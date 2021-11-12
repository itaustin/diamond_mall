<?php

namespace app\api\controller;

use app\common\model\PointsCaptial;

class Points extends Controller
{
    public function getList(){
        $type = input("type");
        $model = new PointsCaptial();
        empty($type) ? 0 : $model->where("type", $type);
        $userInfo = $this->getUser();
        $model
            ->with(['order','user'])
            ->where("is_delete", 0)
            ->order("points_id DESC")
            ->where("user_id", $userInfo["user_id"]);
        $data = $model->select();
        return $this->renderSuccess($data, "success");
    }
}