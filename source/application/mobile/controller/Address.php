<?php
/**
 * date: 2020/2/24 10:52 上午
 * @author 佐威网络科技-PaulAustin
 * @version v1.0
 * Description:
 */
namespace app\mobile\controller;

class Address extends Controller{

    public function index(){
        $this->redirect("/?s=/mobile/address/address_list");
    }

    public function address_list(){
        return $this->fetch("address_list");
    }

    public function address_edit(){
        return $this->fetch("address_edit");
    }

    public function address_add(){
        return $this->fetch("address_add");
    }

}