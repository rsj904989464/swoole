<?php
require __DIR__."/../../vendor/autoload.php";
use Rsj\Io\ReactorHeartBeat\Worker;
$host = 'tcp://0.0.0.0:9999';
$server = new Worker($host);
$server->onConnect = function ($client){
    echo "有一个连接".(int)$client."\n";
};
$server->onReceive = function ($socket,$client,$data){
    $socket->send($client,"服务端：已收到\n");
};

$server->start();