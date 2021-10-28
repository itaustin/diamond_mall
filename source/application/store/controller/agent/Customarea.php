<?php
namespace app\store\controller\agent;

use app\store\controller\Controller;
use app\store\model\CustomArea as CustomAreaModel;
use think\Db;

class Customarea extends Controller{
    public function index($search = ''){
        $model = new CustomAreaModel;
        return $this->fetch('index', [
            'list' => $model->getList($search),
        ]);
    }

    /**
     * 添加管理员
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add()
    {
        $model = new CustomAreaModel;
        if (!$this->request->isAjax()) {
            // 地区列表
            $list = Db::table("zuowey_region")->where("pid",0)->select();
            return $this->fetch('add', compact('list'));
        }
        // 新增记录
        if ($model->add($this->postData('custom_area'))) {
            return $this->renderSuccess('添加成功', url('agent.customarea/index'));
        }
        return $this->renderError($model->getError() ?: '添加失败');
    }

    /**
     * 更新管理员
     * @param $area_id
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit($area_id)
    {
        // 管理员详情
        $model = CustomAreaModel::detail($area_id);
        if (!$this->request->isAjax()) {
            $list = Db::table("zuowey_region")->where("pid",0)->select();
            return $this->fetch('edit', [
                'model' => $model,
                'list' => $list
            ]);
        }
        // 更新记录
        if ($model->edit($this->postData('custom_area'))) {
            return $this->renderSuccess('更新成功', url('agent.customarea/index'));
        }
        return $this->renderError($model->getError() ?: '更新失败');
    }

    /**
     * 删除管理员
     * @param $area_id
     * @return array
     * @throws \think\exception\DbException
     */
    public function delete($area_id)
    {
        // 管理员详情
        $model = CustomAreaModel::detail($area_id);
        if (!$model->setDelete()) {
            return $this->renderError($model->getError() ?: '删除失败');
        }
        return $this->renderSuccess('删除成功');
    }
}