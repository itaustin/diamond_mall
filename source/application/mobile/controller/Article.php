<?php
/**
 * date: 2020/3/2 8:57 下午
 * @author 佐威网络科技-PaulAustin
 * @version v1.0
 * Description:
 */
namespace app\mobile\controller;

class Article extends Controller
{
    public function newsInfo(){
        $aid = input('aid');
        $this->assign("article",\app\api\model\Article::get($aid));
        return $this->fetch("newsInfo");
    }
}