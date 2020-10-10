<?php
$remote_socket = stream_socket_client("tcp://127.0.0.1:9999");
fwrite($remote_socket,"我是客户端\n");
echo fread($remote_socket,65535);