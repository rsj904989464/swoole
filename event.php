<?php
$eventBase = new EventBase(); //event事件库
echo date('H:i:s').PHP_EOL;
$event = new Event( $eventBase, -1, Event::TIMEOUT , function(){
    echo date('H:i:s').PHP_EOL;
});
/**
 * public Event::__construct ( EventBase $base , mixed $fd , int $what , callable $cb [, mixed $arg = NULL ] )
 *  $fd    stream resource,
 *         socket resource,
 *          -1 表示计时器
 *  $what  event flag , Event::TIMEOUT 什么时候执行 , Event::PERSIST 循环执行
 *  $cb    执行的回调函数
 */
$tick = 2;
$event->add( $tick ); //添加事件到事件库
$eventBase->loop();//执行事件库中事件