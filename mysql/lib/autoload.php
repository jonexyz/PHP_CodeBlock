<?php
/**
 * Created by PhpStorm.
 * User: JONE
 * DateTime: 2018/9/29 22:21
 * Email: abc@jone.xyz
 * Description: 类文件自动加载
 */

spl_autoload_register(function ($name) {
    require_once './lib/'.$name.'.class.php';
});