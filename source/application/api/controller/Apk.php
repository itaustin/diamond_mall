<?php


namespace app\api\controller;


use app\api\model\CheckVersion;

class Apk extends Controller
{

    public function checkUpdate()
    {
        $model = new CheckVersion();
        $versionCode = input("appVersion");
        $data = $model->check($versionCode);
        return [
            "update" => $data['state'],
            "new_version" => $data['version_name'],
            "apk_file_url" => $data['apk_url'],
            "update_log" => $data['version_content'],
            "target_size" => bcdiv($data['apk_size'], 1024, 2) . "M",
            "new_md5" => $data['apk_md5'],
            "constraint" => true
        ];
    }

}