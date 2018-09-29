<?php
/**
 * Created by PhpStorm.
 * User: JONE
 * DateTime: 2018/9/29 22:27
 * Email: abc@jone.xyz
 * Description: 单例模式
 */

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

        return self::$obj;
    }

}