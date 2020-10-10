<?php
$client = new swoole_client(SWOOLE_SOCK_TCP);
//连接到服务器
if (!$client->connect('127.0.0.1', 9501, 0.5)){ //0.5表示多久没有连接会报错提示
    die("connect failed.");
}
for($i=1;$i<=10;$i++){  //此处发送的数据会当成一次发送
    $length_pack = pack('n',strlen($i)); //n 16位无符号短整型，占用2字节，所以每次发送数据都会多出2字节
    $client->send($length_pack.$i);
}

//$client->set(array(
//    'open_length_check'     => true,
//    'package_max_length'    => 81920,
//    'package_length_type'   => 'n',
//    'package_length_offset' => 0,
//    'package_body_offset'   => 2,
//));

//$client->set(array(
////    'open_eof_check' => true,
//    'open_eof_split' => true,
//    'package_eof' => "\r\n",
//));
//从服务器接收数据
//$data = $client->recv();


//关闭连接
$client->close();
echo "其他事情\n";

