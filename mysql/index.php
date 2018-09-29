<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-09-29
 * Time: 14:24
 */



require_once './lib/autoload.php';

$mysql = Db::init();

$res = $mysql->tableName('a1')->where(1)->delete();
echo '<pre>';
var_dump($res);