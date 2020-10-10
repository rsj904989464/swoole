<?php
/**
 *  TCP粘包问题：tcp数据发送存在缓存，极短时间内连续发送会当成一次发送，导致数据粘在一起
 *  解决：pack、unpack
 */
//创建Server对象，监听 127.0.0.1:9501端口
$host = '127.0.0.1';
$port = 9501;
$serv = new Swoole\Server($host, $port);

//方法一 swoole自带的，data自动unpack  推荐使用
$serv->set(array(
    'open_length_check'     => true,
    'package_max_length'    => 81920,
    'package_length_type'   => 'n',
    'package_length_offset' => 0,
    'package_body_offset'   => 2,
));

//监听连接进入事件
$serv->on('Connect', function ($serv, $fd) {
    echo date('H:i:s')." Client".(int)$fd.": Connect.\n";
});

//监听数据接收事件
$serv->on('Receive', function ($serv, $fd, $from_id, $data) {
    //方法二，手动unpack，问题：会不会存在unpack内容发送一半,导致unpack失败
//    while($data){
//        $pack_data = unpack('n',substr($data,0,2));
//        echo substr($data,2,$pack_data[1])."\n";
//        $data = substr($data,2+$pack_data[1]);
//    }

    echo $data."\n";
});

echo $host.':'.$port."\n";
//启动服务器
$serv->start();