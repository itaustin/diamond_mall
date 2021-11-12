<?php

namespace app\api\controller;

use app\api\model\WxappCategory as WxappCategoryModel;
use app\store\model\PointsCategory as PointsCategoryModel;

/**
 * 积分商品分类控制器
 * Class Goods
 * @package app\api\controller
 */
class PointsCategory extends Controller
{
    /**
     * 分类页面
     * @return array
     * @throws \think\exception\DbException
     */
    public function index()
    {
        // 分类模板
        $templet = WxappCategoryModel::detail();
        // 商品分类列表
        $list = array_values(PointsCategoryModel::getCacheTree());
        return $this->renderSuccess(compact('templet', 'list'));
    }

    public function getChild(){
        $model = new PointsCategoryModel;
        $data = $model->where("parent_id", input("category_id"))
            ->with(['image'])
            ->select();
        return $this->renderSuccess($data,"获取成功");
    }

    public function getList(){
        $model = new PointsCategoryModel;
        $data = $model
            ->select();
        return $this->renderSuccess($data);
    }
}
