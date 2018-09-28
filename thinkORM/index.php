<?php

const PATH =  __DIR__; // 定义网站根目录

include './model/Users.php'; // 引入需要使用的模型文件

$a = Users::select()->toArray();

var_dump($a);