<?php
/**
 * date: 2020/5/20 8:34 上午
 * @author 佐威网络科技-PaulAustin
 * @version v1.0
 * Description:
 */

/**
 * date: 2020/4/19 5:25 下午
 * @author 佐威网络科技-PaulAustin
 * @version v1.0
 * Description:
 */

namespace app\mobile\controller;

use Endroid\QrCode\QrCode as QrCodeModel;

class Qrcode extends \think\Controller
{
    public function view($url, $size = 300, $mobile = '', $avatarUrl = null)
    {
        $url = str_replace('--', '&', $url);
        $qrcode = new QrCodeModel();
        $qrcode
            ->setLabelFontPath(VENDOR_PATH . 'endroid/qrcode/assets/noto_sans.otf')
//            ->setLogoPath(ROOT_PATH.DS.'/../web/static/logo/logo.png')
            ->setLogoWidth(120)
            ->setErrorCorrectionLevel('high')
            ->setText($url)
            ->setSize($size)
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setLabelFontSize(16);
        if ($avatarUrl !== null) {
            $qrcode->setLogoPath($avatarUrl);
        }
        if ($mobile !== '') {
            $qrcode->setLabel('邀请码&手机号：' . $mobile);
            $qrcode->setLabelFontSize(14);
        }
        header('Content-Type: ' . $qrcode->getContentType());
        echo $qrcode->writeString();
        exit;
    }
}