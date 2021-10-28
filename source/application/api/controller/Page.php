<?php

namespace app\api\controller;

use app\api\model\WxappPage;

/**
 * 页面控制器
 * Class Index
 * @package app\api\controller
 */
class Page extends Controller
{
    /**
     * 页面数据
     * @param null $page_id
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index($page_id = null)
    {
        // 页面元素
        $data = WxappPage::getPageDataForAndroid($this->getUser(false), $page_id);
        return $this->renderSuccess($data);
    }

    public function goods($page_id = null){
        $data = WxappPage::getPageDataForAndroid($this->getUser(false), $page_id);
        $initData = [
            "search" => $data["items"][0],
            "banner" => $data["items"][1],
            "goods" => $data["items"][2],
        ];
        return [
            "code" => 1,
            "msg" => "获取成功",
            "dataList" => $initData
        ];
    }

}
