<?php
/**
 *  测试心跳，从connect到close时间间隔是10s
 */
//创建Server对象，监听 127.0.0.1:9501端口
$host = '127.0.0.1';
$port = 9501;
$serv = new Swoole\Server($host, $port);

$serv->set([
    //心跳检测,每三秒检测一次，10秒没活动就断开
    'heartbeat_idle_time'=>10,//连接最大的空闲时间
    'heartbeat_check_interval'=>3 //服务器定时检查
]);

//监听连接进入事件
$serv->on('Connect', function ($serv, $fd) {
    echo date('H:i:s')." Client".(int)$fd.": Connect.\n";
});

//监听数据接收事件
$serv->on('Receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, "Server: ".$data);
});

//监听连接关闭事件
$serv->on('Close', function ($serv, $fd) {
    echo date('H:i:s')." Client".(int)$fd.": Close.\n";
});

echo $host.':'.$port."\n";

//启动服务器
$serv->start();