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

namespace app\api\controller;

use Endroid\QrCode\QrCode as QrCodeModel;
use think\Controller;

class Qrcode extends Controller
{
    public function view($url = "", $size = 300, $invite_code = '', $avatarUrl = null)
    {
        $url = str_replace('--', '&', $url);
        $qrcode = new QrCodeModel();
        $qrcode->setLabelFontPath(VENDOR_PATH . 'endroid/qrcode/assets/noto_sans.otf')
//            ->setLogoPath(ROOT_PATH.'../web/static/public/images/logo.png')
            ->setLogoWidth(68)
            ->setErrorCorrectionLevel('high')
            ->setText($url)
            ->setSize($size)
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setLabelFontSize(16);
        $qrcode->setLabel('邀请码：' . $invite_code);
        $qrcode->setLabelFontSize(14);
        header('Content-Type: ' . $qrcode->getContentType());
        echo $qrcode->writeString();
        exit;
    }

    public function view_android($size = 300, $invite_code = '')
    {
        $qrcode = new QrCodeModel();
        $qrcode->setLabelFontPath(VENDOR_PATH . 'endroid/qrcode/assets/noto_sans.otf')
//            ->setLogoPath(ROOT_PATH.'../web/static/public/images/logo.png')
            ->setLogoWidth(68)
            ->setErrorCorrectionLevel('high')
            ->setText($invite_code)
            ->setSize($size)
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setLabelFontSize(16);
        $qrcode->setLabel('邀请码：' . $invite_code);
        $qrcode->setLabelFontSize(14);
        header('Content-Type: ' . $qrcode->getContentType());
        echo $qrcode->writeString();
        exit;
    }
}