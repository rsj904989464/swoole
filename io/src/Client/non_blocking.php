<?php
$remote_socket = stream_socket_client("tcp://127.0.0.1:9999");
stream_set_blocking($remote_socket,0);
fwrite($remote_socket,"我是非阻塞客户端\n");
echo '执行结束';

$r = 1;
while (is_resource($remote_socket) && !feof($remote_socket)) {
// 接收的数据包的大小65535
    var_dump(fread($remote_socket, 65535));
    echo $r++."\n";
    sleep(1);
}