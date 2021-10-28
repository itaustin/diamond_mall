<?php

namespace app\common\model;

use app\common\exception\BaseException;
use think\Cache;

/**
 * 微信公众号模型
 * Class Wxapp
 * @package app\common\model
 */
class Wxapp extends BaseModel
{
    protected $name = 'wxapp';

    /**
     * 公众号导航
     * @return \think\model\relation\HasOne
     */
    public function navbar()
    {
        return $this->hasOne('WxappNavbar');
    }

    /**
     * 公众号页面
     * @return \think\model\relation\HasOne
     */
    public function diyPage()
    {
        return $this->hasOne('WxappPage');
    }

    /**
     * 获取公众号信息
     * @param null $wxapp_id
     * @return static|null
     * @throws \think\exception\DbException
     */
    public static function detail($wxapp_id = null)
    {
        return self::get($wxapp_id ?: []);
    }

    /**
     * 从缓存中获取公众号信息
     * @param null $wxapp_id
     * @return mixed|null|static
     * @throws BaseException
     * @throws \think\exception\DbException
     */
    public static function getWxappCache($wxapp_id = null)
    {
        if (is_null($wxapp_id)) {
            $self = new static();
            $wxapp_id = $self::$wxapp_id;
        }
        if (!$data = Cache::get('wxapp_' . $wxapp_id)) {
            $data = self::detail($wxapp_id);
            if (empty($data)) throw new BaseException(['msg' => '未找到当前公众号信息']);
            Cache::tag('cache')->set('wxapp_' . $wxapp_id, $data);
        }
        return $data;
    }

}
