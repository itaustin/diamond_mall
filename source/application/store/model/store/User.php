<?php

namespace app\store\model\store;

use think\Session;
use app\common\model\store\User as StoreUserModel;

/**
 * 商家用户模型
 * Class StoreUser
 * @package app\store\model
 */
class User extends StoreUserModel
{
    /**
     * 商家用户登录
     * @param $data
     * @return bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function login($data)
    {
        // 验证用户名密码是否正确
        if (!$user = $this->getLoginUser($data['user_name'], $data['password'])) {
            $this->error = '登录失败, 用户名或密码错误';
            return false;
        }
        if (empty($user['wxapp'])) {
            $this->error = '登录失败, 未找到公众号信息';
            return false;
        }
        if ($user['wxapp']['is_recycle']) {
            $this->error = '登录失败, 当前公众号商城已删除';
            return false;
        }
        // 保存登录状态
        $this->loginState($user);
        return true;
    }

    /**
     * 获取登录用户信息
     * @param $user_name
     * @param $password
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function getLoginUser($user_name, $password)
    {
        return self::useGlobalScope(false)->with(['wxapp'])->where([
            'user_name' => $user_name,
            'password' => zuowey_hash($password),
            'is_delete' => 0
        ])->find();
    }

    /**
     * 获取用户列表
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getList()
    {
        return $this->where('is_delete', '=', '0')
            ->order(['create_time' => 'desc'])
            ->paginate(15, false, [
                'query' => \request()->request()
            ]);
    }

    /**
     * 新增记录
     * @param $data
     * @return bool|false|int
     * @throws \think\exception\DbException
     */
    public function add($data)
    {
        if (self::checkExist($data['user_name'])) {
            $this->error = '用户名已存在';
            return false;
        }
        if ($data['password'] !== $data['password_confirm']) {
            $this->error = '确认密码不正确';
            return false;
        }
        if (empty($data['role_id'])) {
            $this->error = '请选择所属角色';
            return false;
        }
        $this->startTrans();
        try {
            // 新增管理员记录
            $data['password'] = zuowey_hash($data['password']);
            $data['wxapp_id'] = !empty(self::$wxapp_id) ? self::$wxapp_id : 10001;
            $data['is_super'] = 0;
            $this->allowField(true)->save($data);
            // 新增角色关系记录
            (new UserRole)->add($this['store_user_id'], $data['role_id']);
            $this->commit();
            return $this['user_name'];
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
        if ($this['user_name'] !== $data['user_name']
            && self::checkExist($data['user_name'])) {
            $this->error = '用户名已存在';
            return false;
        }
        if (!empty($data['password']) && ($data['password'] !== $data['password_confirm'])) {
            $this->error = '确认密码不正确';
            return false;
        }
        if (empty($data['role_id'])) {
            $this->error = '请选择所属角色';
            return false;
        }
        if (!empty($data['password'])) {
            $data['password'] = zuowey_hash($data['password']);
        } else {
            unset($data['password']);
        }
        $this->startTrans();
        try {
            // 更新管理员记录
            $this->allowField(true)->save($data);
            // 更新角色关系记录
            (new UserRole)->edit($this['store_user_id'], $data['role_id']);
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
        if ($this['is_super']) {
            $this->error = '超级管理员不允许删除';
            return false;
        }
        // 删除对应的角色关系
        UserRole::deleteAll(['store_user_id' => $this['store_user_id']]);
        return $this->save(['is_delete' => 1]);
    }

    /**
     * 更新当前管理员信息
     * @param $data
     * @return bool
     */
    public function renew($data)
    {
        if ($data['password'] !== $data['password_confirm']) {
            $this->error = '确认密码不正确';
            return false;
        }
        if ($this['user_name'] !== $data['user_name']
            && self::checkExist($data['user_name'])) {
            $this->error = '用户名已存在';
            return false;
        }
        // 更新管理员信息
        if ($this->save([
                'user_name' => $data['user_name'],
                'password' => zuowey_hash($data['password']),
            ]) === false) {
            return false;
        }
        // 更新session
        Session::set('zuowey_store.user', [
            'store_user_id' => $this['store_user_id'],
            'user_name' => $data['user_name'],
        ]);
        return true;
    }

    /**
     * 资金冻结
     * @param $money
     * @return false|int
     */
    public function freezeMoney($money)
    {
        return $this->save([
            'money' => $this['money'] - ($money+3),
            'freeze_money' => $this['freeze_money'] + ($money),
        ]);
    }

    /**
     * 提现打款成功：累积提现佣金
     * @param $user_id
     * @param $money
     * @return false|int
     * @throws \think\exception\DbException
     */
    public static function totalMoney($user_id, $money)
    {
        $model = self::detail($user_id);
        return $model->save([
            'freeze_money' => $model['freeze_money'] - $money,
            'total_money' => $model['total_money'] + ($money+3),
        ]);
    }

    /**
     * 提现驳回：解冻分销商资金
     * @param $user_id
     * @param $money
     * @return false|int
     * @throws \think\exception\DbException
     */
    public static function backFreezeMoney($user_id, $money)
    {
        $model = self::detail($user_id);
        return $model->save([
            'money' => ($model['money'] + $money)+3,
            'freeze_money' => $model['freeze_money'] - $money,
        ]);
    }
}
