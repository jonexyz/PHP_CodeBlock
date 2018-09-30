<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-09-30
 * Time: 17:05
 */

interface Transport
{
    public function go();
}

class Bus implements Transport
{
    public function go()
    {
        echo '我是公交';
    }
}

class Car implements Transport
{
    public function go()
    {
       echo '我是小汽车';
    }
}

class back implements Transport
{
    public function go()
    {
        echo '我是自行车';
    }
}

class transFactory
{
    public static function factory($t)
    {
        switch ($t) {
            case 'bus':
                return new Bus();
                break;

            case 'car':
                return new Car();
                break;

            case 'bike':
                return new back();
                break;

        }

    }
}

$tra = transFactory::factory('car');
$tra->go();