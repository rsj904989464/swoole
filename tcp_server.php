<?php
//创建Server对象，监听 127.0.0.1:9501端口
$host = '127.0.0.1';
$port = 9501;
$serv = new Swoole\Server($host, $port);

//监听连接进入事件
$serv->on('Connect', function ($serv, $fd) {
    echo "Client: Connect.\n";
});

//监听数据接收事件
$serv->on('Receive', function ($serv, $fd, $from_id, $data) {
//    sleep(3);
    $serv->send($fd, "Server: ".$data);
    var_dump($data);
});

//监听连接关闭事件
$serv->on('Close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

$serv->on('workerStart', function () {
    echo "workerStart \n";
});
$serv->on('managerStart', function () {
    echo "managerStart \n";
});
$serv->on('start', function () {  //启动服务
    echo "start \n";
});
$serv->set([
    'worker_num' => 2
]);


echo $host.':'.$port."\n";
//启动服务器
$serv->start();