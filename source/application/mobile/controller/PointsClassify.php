<?php
/**
 * date: 2020/2/22 1:46 下午
 * @author 佐威网络科技-PaulAustin
 * @version v1.0
 * Description:
 */
namespace app\mobile\controller;

class PointsClassify extends Controller{

    public function index(){
        return $this->fetch("index");
    }

    public function lists(){
        return $this->fetch("lists");
    }

}