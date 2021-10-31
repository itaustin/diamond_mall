<?php


namespace app\api\controller;


class InviteCode extends \think\Controller
{
    /**
     * @var string
     */
    private $key; // Array.from(s).sort(() => Math.random() > 0.5 ? -1 : 1).join('')

    /**
     * @var int
     */
    private $octal;

    /**
     * @var int
     */
    private $base = 0;

    public function __construct($key = 'BM2DYTOH8ZC7EN3XJ16LISPGWQ5UKRFVA9', $base = 9876543210)
    {
        // 注意这个key里面不能出现数字 0，否则当`求模=0`会重复的
        $this->key = $this->clear($key);
        // 多少进制
        $this->octal = strlen($this->key);
        // 加一个基数，避免出现补零
        $this->base = $base;
    }

    /**
     * encode id to code
     *
     * @param integer $id
     * @param integer $length
     * @return string
     */
    public function encode($id, $length = 6)
    {
        $code = '';
        // 转进制
        $num = $this->base + $id;
        while ($num > 0) {
            $mod = $num % $this->octal; // 求模
            $num = ($num - $mod) / $this->octal;
            $code = $this->key[$mod] . $code;
        }
        return str_pad($code, $length, '0', STR_PAD_LEFT); // 不足用0补充;
    }

    /**
     * decode code to id
     *
     * @param string $code
     * @return integer
     */
    public function decode($code)
    {
        //移除左侧的 0
        if (strrpos($code, '0') !== false) {
            $code = substr($code, strrpos($code, '0') + 1);
        }
        $len = strlen($code);
        $code = strrev($code);
        $num = 0;
        for ($i = 0; $i < $len; $i++) {
            $num += strpos($this->key, $code[$i]) * pow($this->octal, $i);
        }
        return (int)$num - $this->base;
    }

    /**
     * @param  string  $str
     *
     * @return string
     */
    private function clear($str)
    {
        $uniqueStr = trim(implode('', array_unique(str_split($str))));

        return str_replace('0', '', $uniqueStr);
    }
}