<?php
/**
 * Server中对象的4层生命周期
 */


//创建Server对象，监听 127.0.0.1:9501端口
$host = '127.0.0.1';
$port = 9501;
$serv = new Swoole\Server($host, $port,SWOOLE_BASE);

//$serv->on('start', function () {  //启动服务
//    echo "start \n";
//});

//监听连接进入事件
$serv->on('Connect', function ($serv, $fd) {
    require_once 'Test.php';
    global $object ;
    if(!$object){
        $object = new Test();
    }
});

//监听数据接收事件
$serv->on('Receive', function ($serv, $fd, $from_id, $data) {
    global $object;
    var_dump($object);
    $object->index();
});

//监听连接关闭事件
$serv->on('Close', function ($serv, $fd) {
    echo " Client: Close.\n";
});

$serv->set(array(
    'worker_num' => 2,
));

echo $host.':'.$port."\n";
//启动服务器
$serv->start();