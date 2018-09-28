<?php

use think\Model;
use think\Db;



include PATH.'/vendor/autoload.php';

class BaseModel extends Model
{

    protected static function init()
    {

        $config = include PATH.'/model/config.php';

        Db::setConfig($config);

    }


}