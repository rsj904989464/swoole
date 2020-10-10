<?php
require __DIR__."/../../vendor/autoload.php";
use Rsj\Io\ReactorPlus\Worker;
use Rsj\Io\ReactorPlus\InotifyTest;
$host = 'tcp://0.0.0.0:9999';
$server = new Worker($host);
$server->set(['watch_file'=>1]);

$server->onConnect = function ($client){
    view('当前进程：'.posix_getpid());
    (new InotifyTest())->index(); //用来测试inotify
};
$server->onReceive = function ($socket,$client,$data){
    $socket->send($client,"服务端：已收到\n");
};

$server->start();