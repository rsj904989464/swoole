<?php
//创建Server对象，监听 127.0.0.1:9501端口，类型为SWOOLE_SOCK_UDP
$serv = new swoole_server("127.0.0.1", 9501, SWOOLE_PROCESS, SWOOLE_SOCK_UDP);

//监听数据接收事件
$serv->on('Packet', function ($serv, $data, $clientInfo) {
    var_dump($clientInfo);
    $serv->sendto($clientInfo['address'], $clientInfo['port'], "Server ".$data);
});

//启动服务器
$serv->start();