<?php

/*
观察者接口
*/
interface InterfaceObserver
{
    function onListen($sender, $args);
    function getObserverName();
}

// 可被观察者接口
interface InterfaceObservable
{
    function addObserver($observer);
    function removeObserver($observer_name);
}

// 观察者抽象类
abstract class Observer implements InterfaceObserver
{
    protected $observer_name;

    function getObserverName()
    {
        return $this->observer_name;
    }

    function onListen($sender, $args)
    {

    }
}

// 可被观察类
abstract class Observable implements InterfaceObservable
{
    protected $observers = array();

    public function addObserver($observer)
    {
        if ($observer instanceof InterfaceObserver)
        {
            $this->observers[] = $observer;
        }
    }

    public function removeObserver($observer_name)
    {
        foreach ($this->observers as $index => $observer)
        {
            if ($observer->getObserverName() === $observer_name)
            {
                array_splice($this->observers, $index, 1);
                return;
            }
        }
    }
}

// 模拟一个可以被观察的类
class A extends Observable
{
    public function addListener($listener)
    {
        foreach ($this->observers as $observer)
        {
            $observer->onListen($this, $listener);
        }
    }
}

// 模拟一个观察者类
class B extends Observer
{
    protected $observer_name = 'B';

    public function onListen($sender, $args)
    {
        var_dump($sender);
        echo "<br>";
        var_dump($args);
        echo "<br>";
    }
}

// 模拟另外一个观察者类
class C extends Observer
{
    protected $observer_name = 'C';

    public function onListen($sender, $args)
    {
        var_dump($sender);
        echo "<br>";
        var_dump($args);
        echo "<br>";
    }
}

$a = new A();
// 注入观察者
$a->addObserver(new B());
$a->addObserver(new C());

// 可以看到观察到的信息
$a->addListener('D');

// 移除观察者
$a->removeObserver('B');

// 打印的信息：
// object(A)#1 (1) { ["observers":protected]=> array(2) { [0]=> object(B)#2 (1) { ["observer_name":protected]=> string(1) "B" } [1]=> object(C)#3 (1) { ["observer_name":protected]=> string(1) "C" } } }
// string(1) "D"
// object(A)#1 (1) { ["observers":protected]=> array(2) { [0]=> object(B)#2 (1) { ["observer_name":protected]=> string(1) "B" } [1]=> object(C)#3 (1) { ["observer_name":protected]=> string(1) "C" } } }
// string(1) "D"