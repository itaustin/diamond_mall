<?php
namespace app\common\model;

use think\Model;

class CheckVersion extends Model
{
    protected $autoWriteTimestamp = true;
    protected $createTime = "create_time";
    protected $updateTime = "update_time";


}