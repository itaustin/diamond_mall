<?php

namespace app\api\controller;

use app\api\model\Wxapp as WxappModel;
use app\api\model\WxappHelp;

/**
 * 微信公众号
 * Class Wxapp
 * @package app\api\controller
 */
class Wxapp extends Controller
{
    /**
     * 公众号基础信息
     * @return array
     */
    public function base()
    {
//        $wxapp = WxappModel::getWxappCache();
        return $this->renderSuccess([]);
    }

    /**
     * 帮助中心
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function help()
    {
        $model = new WxappHelp;
        $list = $model->getList();
        return $this->renderSuccess(compact('list'));
    }

}
