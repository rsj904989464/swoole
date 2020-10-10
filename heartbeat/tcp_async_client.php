<?php
use Swoole\Async\Client;
$client = new Client(SWOOLE_SOCK_TCP);

$client->on("connect", function(Client $cli){

});

$client->on("receive", function(Client $cli, $data){
    echo date('H:i:s')." Receive: $data\n";
});

$client->on("close", function(Client $cli){
    echo "Connection close\n";
});

$client->on("error", function(Client $cli){

});

$client->connect('127.0.0.1', 9501);
echo "die \n";

swoole_timer_tick(2000, function ($timer_id) use ($client) {
    $client->send(1);
});
