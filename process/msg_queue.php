<?php
/**
 *  问题：在父进程空间中，如果push在start之后
 *       在父进程中的pop就会接收到自己push的内容
 */
use Swoole\Process;

for ($i=0; $i < 3; $i++) {
    $process = new Swoole\Process(function($process){
        // 进程空间
        echo '子：'.$process->pop()."\n";
//        $process->push('push_hello2');
    }, false, true);

    $process->useQueue(1,2 | swoole_process::IPC_NOWAIT);//设置为非阻塞

    $process->push('push_hello1');

    $pid = $process->start();

//    sleep(1);

    echo '父：'.$process->pop()."\n";
}


