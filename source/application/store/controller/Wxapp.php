<?php

namespace app\store\controller;

use app\store\model\Wxapp as WxappModel;
use app\store\model\WxappNavbar as WxappNavbarModel;

/**
 * 公众号管理
 * Class Wxapp
 * @package app\store\controller
 */
class Wxapp extends Controller
{
    /**
     * 公众号设置
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function setting()
    {
        // 当前公众号信息
        $model = WxappModel::detail();
        if (!$this->request->isAjax()) {
            return $this->fetch('setting', compact('model'));
        }
        // 更新公众号设置
        if ($model->edit($this->postData('wxapp'))) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失败');
    }

    /**
     * 导航栏设置
     * @return array|mixed
     * @throws \think\exception\DbException
     */
    public function tabbar()
    {
        $model = WxappNavbarModel::detail();
        if (!$this->request->isAjax()) {
            return $this->fetch('tabbar', compact('model'));
        }
        $data = $this->postData('tabbar');
        if (!$model->edit($data)) {
            return $this->renderError('更新失败');
        }
        return $this->renderSuccess('更新成功');
    }

}
