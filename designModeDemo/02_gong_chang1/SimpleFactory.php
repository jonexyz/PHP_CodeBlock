<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-09-30
 * Time: 16:55
 */

// 工厂类,简而言之就是用来批量生产对象的类
class SimpleFactory
{
    public static function factory()
    {
        return new SimpleFactory();
    }
}


$instance1  = SimpleFactory::factory();
$instance2  = SimpleFactory::factory();
$instance3  = SimpleFactory::factory();

var_dump($instance1);
var_dump($instance2);
var_dump($instance3);