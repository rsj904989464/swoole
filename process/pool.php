<?php
/**
 *  1.不停的创建进程、结束进程，但是进程数量固定
 *  2.kill主进程、子进程会变孤儿进程
 */
$workerNum = 5;
$pool = new Swoole\Process\Pool($workerNum);
$pool->on('WorkerStart', function($pool, $workerId){
    echo '执行 WorkerStart # '.$workerId."\n";
    sleep(10);
});
$pool->on('WorkerStop', function($pool, $workerId){
    echo '执行 WorkerStop # '.$workerId."\n";
});
$pool->start();




