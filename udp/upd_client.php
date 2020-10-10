<?php
$client = new Swoole\Client(SWOOLE_SOCK_UDP);
if (!$client->connect('127.0.0.1', 9501, -1)) {
    exit("connect failed. Error: {$client->errCode}\n");
}
$client->send("udp_client\n");

echo $client->recv();

$client->close();