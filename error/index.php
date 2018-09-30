<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-09-30
 * Time: 15:25
 */



spl_autoload_register(function ($name) {
    require_once './src/'.$name.'.php';
});

