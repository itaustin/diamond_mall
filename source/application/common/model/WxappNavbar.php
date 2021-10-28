<?php

namespace app\common\model;

/**
 * 微信公众号导航栏模型
 * Class WxappNavbar
 * @package app\common\model
 */
class WxappNavbar extends BaseModel
{
    protected $name = 'wxapp_navbar';

    /**
     * 顶部导航文字颜色
     * @param $value
     * @return array
     */
    public function getTopTextColorAttr($value)
    {
        $color = [10 => '#000000', 20 => '#ffffff'];
        return ['text' => $color[$value], 'value' => $value];
    }

    /**
     * 公众号导航栏详情
     * @return null|static
     * @throws \think\exception\DbException
     */
    public static function detail()
    {
        return self::get([]);
    }

    /**
     * 新增公众号导航栏默认设置
     * @param $wxapp_id
     * @param $wxapp_title
     * @return false|int
     */
    public function insertDefault($wxapp_id, $wxapp_title)
    {
        return $this->save([
            'wxapp_title' => $wxapp_title,
            'top_text_color' => 20,
            'top_background_color' => '#fd4a5f',
            'wxapp_id' => $wxapp_id
        ]);
    }

}
