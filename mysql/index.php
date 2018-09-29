<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-09-29
 * Time: 14:24
 */
include './Db.php';

$mysql = Db::init();

$res = $mysql->tableName('a')->where()->delete();

var_dump($res);