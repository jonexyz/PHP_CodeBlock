<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-10-08
 * Time: 9:58
 */

$a = ['a','b','c','d','e','f'];

array_splice($a, 1, 1, 'aaa');

echo '<pre>';
print_r($a); exit();
/**
 * 观察者接口
 */
interface InterfaceObserver
{
    function onListen($sender, $args); // 要监听的内容
    function getObserverName();  // 获取监听者的类名
}

// 可被观察者接口
interface InterfaceObservable
{
    function addObserver($observer); // 添加观察者
    function removeObserver($observer_name); //移除观察者
}

// 观察者抽象类
abstract class Observer implements InterfaceObserver
{
    protected  $observer_name;

    function getObserverName()
    {
        return $this->observer_name;
    }

    function onListen($sender, $args)
    {

    }
}

//可被观察类
abstract class Observable implements InterfaceObservable
{
    protected  $observers = [];

    public function addObserver($observer)
    {
        if ($observer instanceof InterfaceObserver) {
            $this->observers[] = $observer;
        }
    }

    public function removeObserver($observer_name)
    {
        foreach ($this->observers as $index => $observer){
            if ($observer->getObserverName() === $observer_name) {
                array_splice($this->observers, $index, 1);
                return;
            }
        }

    }
}


class A extends Observable
{
    public function addListener($listener)
    {
        foreach ($this->observers as $observer){
            $observer->onListen($this, $listener);
        }
    }
}

class B extends Observer
{
    protected $observer_name = 'B';

    public function onListen($sender, $args)
    {
        var_dump($sender);
        echo '<br>';

        var_dump($args);
        echo '<br>';
    }
}

class C extends Observer
{
    protected $observer_name = 'C';

    public function onListen($sender, $args)
    {
        var_dump($sender);
        echo '<br>';

        var_dump($args);
        echo '<br>';

    }
}

$a = new A();

$a->addObserver(new B());
$a->addObserver(new C());

//$a->addListener('D');

$a->removeObserver('B');