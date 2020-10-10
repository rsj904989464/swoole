<?php

$pool = new Swoole\Process\Pool(2);

$pool->on('WorkerStart', function($pool, $workerId){
    echo '执行 WorkerStart # '.$workerId."\n";
    $running = true;
    pcntl_signal(SIGTERM, function () use (&$running) {
        $running = false;
    });
    try{
        $redis = new Redis();
        $redis->pconnect('127.0.0.1', 6379);
        $key = "key1";
        while ($running) {
            $msg = $redis->brPop($key , 0);
            var_dump($msg);
            pcntl_signal_dispatch(); // 信号触发，打断
        }
    }catch (Exception $ex){
        echo $ex->getMessage()."\n";
    }
});
$pool->on('WorkerStop', function($pool, $workerId){
    echo '执行 WorkerStop # '.$workerId."\n";
});
$pool->start();