<?php
$client = new swoole_client(SWOOLE_SOCK_TCP);
//连接到服务器
if (!$client->connect('127.0.0.1', 9501, -1)){
    die("connect failed.");
}
//$client->set(array(
//    'open_length_check'     => true,
//    'package_max_length'    => 81920,
//    'package_length_type'   => 'n',
//    'package_length_offset' => 0,
//    'package_body_offset'   => 2,
//));

$client->send('this is tcp_client');
//do{
//    $data = @$client->recv();
//}while(!$data);
//
//var_dump($data);


//向服务器发送数据
//if (!$client->send("======Start======")){
//    die("send failed.");
//}
//$a = 0;
//while ($a < 10){
//    $packdata = pack('n',strlen($a));
//    if($client->send($packdata.$a)) echo $a;
//    $a++;
//}
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

