<?php
/**
 * date: 2020/2/26 6:50 下午
 * @author 佐威网络科技-PaulAustin
 * @version v1.0
 * Description:
 */
namespace app\mobile\controller;

class Dealer extends Controller{
    public function apply(){
        return $this->fetch("apply");
    }
}