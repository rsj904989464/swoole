<?php
$host = "tcp://0.0.0.0:9000";
$server_socket = stream_socket_server($host);
//echo '<pre>';
//var_dump($res);

while (true) {
    $client = stream_socket_accept($server_socket);
    var_dump(fread($client, 65535));
    fwrite($client, "server hellow");
    fclose($client);
    var_dump($client);
}