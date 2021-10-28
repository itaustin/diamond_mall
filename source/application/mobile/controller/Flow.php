<?php
/**
 * date: 2020/2/22 10:14 下午
 * @author 佐威网络科技-PaulAustin
 * @version v1.0
 * Description:
 */
namespace app\mobile\controller;

class Flow extends Controller{
    public function index(){
        return $this->fetch("index");
    }
}