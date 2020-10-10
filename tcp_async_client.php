<?php
/**
 *  异步客户端:主程序先执行完，然后等待分支程序执行
 */
use Swoole\Async\Client;
$client = new Client(SWOOLE_SOCK_TCP);
$client->on("connect", function(Client $cli) {
    $cli->send("GET / HTTP/1.1\r\n\r\n");
});
$client->on("receive", function(Client $cli, $data){
    echo "Receive: $data";
// $cli->send(str_repeat('A', 100)."\n");
// sleep(1);
});
$client->on("error", function(Client $cli){
    echo "error\n";
});
$client->on("close", function(Client $cli){
    echo "Connection close\n";
});
$client->connect('127.0.0.1', 9501);

echo 'die';
