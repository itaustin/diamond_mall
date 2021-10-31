<?php
namespace app\api\model;

use app\common\model\CheckVersion as CheckVersionModel;

class CheckVersion extends CheckVersionModel{
    public function check($version){
        $data = $this->order(
            "version_code DESC"
        )->find();
        if($data['version_code'] > $version){
            $data['state'] = "Yes";
        } else {
            $data['state'] = "No";
        }
        return $data;
    }
}