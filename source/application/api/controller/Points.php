<?php

namespace app\api\controller;

class Points extends Controller
{
    public function getList(){
        return $this->renderSuccess([], "success");
    }
}