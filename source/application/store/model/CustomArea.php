<?php
namespace app\store\model;

use app\common\model\CustomArea as CustomAreaModel;
use think\Db;

class CustomArea extends CustomAreaModel{

    /**
     * 获取自定义区域
     * @param string $search
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getList($search = '')
    {
        // 构建查询规则
        $this->alias('custom_area')
            ->where('custom_area.is_delete', '=', '0')
            ->field('custom_area.*, region.merger_name')
            ->join('region', 'region.id = custom_area.parent_id')
            ->order(['custom_area.create_time' => 'desc']);
        // 查询条件
        !empty($search) && $this->where('custom_area.name', 'like', "%$search%");
        // 获取列表数据
        return $this->paginate(15, false, [
            'query' => \request()->request()
        ]);
    }

    public function add($data){
        $this->startTrans();
        try {
            // 新增管理员记录
            $data['parent_id'] = $data['region_id'];
            $data['parent_merger_name'] = Db::table("zuowey_region")->where("id",$data['region_id'])->field('merger_name')->find()['merger_name'];
            $data['wxapp_id'] = self::$wxapp_id;
            $data['is_super'] = 0;
            $this->allowField(true)->save($data);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 更新记录
     * @param array $data
     * @return bool
     * @throws \think\exception\DbException
     */
    public function edit($data)
    {
        $this->startTrans();
        try {
            $data['parent_id'] = $data['region_id'];
            $data['parent_merger_name'] = Db::table("zuowey_region")->where("id",$data['region_id'])->field('merger_name')->find()['merger_name'];
            // 更新自定义区域记录
            $this->allowField(true)->save($data);
            // 更新角色关系记录
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 软删除
     * @return false|int
     */
    public function setDelete()
    {
        return $this->save(['is_delete' => 1]);
    }
}