<?php

use think\Db;
const PATH =  __DIR__; // 定义网站根目录

include './vendor/autoload.php';
// 数据库配置信息设置（全局有效）
$cfg = include './model/config.php';
Db::setConfig($cfg);

var_dump(Db::name('users')->find(1));