<?php
/**
 * Created by PhpStorm.
 * User: 12
 * Date: 2018/9/29
 * Time: 20:00
 */

// 生产单例
class Singleton
{
    private static $obj;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function create()
    {
        if (!self::$obj instanceof self) {
            self::$obj = new self();
        }

        //var_dump(self::$obj);
        return self::$obj;
    }

}