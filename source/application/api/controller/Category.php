<?php

namespace app\api\controller;

use app\api\model\Category as CategoryModel;
use app\api\model\WxappCategory as WxappCategoryModel;

/**
 * 商品分类控制器
 * Class Goods
 * @package app\api\controller
 */
class Category extends Controller
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
        $list = array_values(CategoryModel::getCacheTree());
        foreach ($list as $key => &$value){
            if($value['category_id'] == 10006){
                unset($list[$key]);
            }
        }
        $list = array_values($list);
        return $this->renderSuccess(compact('templet', 'list'));
    }

    public function getList(){
        $model = new CategoryModel();
        $data = $model->where("category_id", "not in", "10006")
            ->select();
        return $this->renderSuccess($data);
    }
}
