<?php
/**
 *  1.父进程先执行完，才执行子进程
 *  2.三个子进程是同时执行的
    start:1
    end:1
    start:2
    end:2
    start:3
    end:3
    Child #83133 start and sleep 1s
    Child #83134 start and sleep 2s
    Child #83135 start and sleep 3s
    Child #83133 exit
    Child #83134 exit
    Child #83135 exit
    Recycled #83135, code=0, signal=0
    Recycled #83133, code=0, signal=0
    Recycled #83134, code=0, signal=0
    Parent #83132 exit
 */
use Swoole\Process;

for ($n = 1; $n <= 3; $n++) {
    $process = new Process(function () use ($n) {
        echo 'Child #' . getmypid() . " start and sleep {$n}s" . PHP_EOL;
        sleep(3);
        echo 'Child #' . getmypid() . ' exit' . PHP_EOL;
    });
    echo 'start:'.$n."\n";
    $process->start();
    echo 'end:'.$n."\n";
}


for ($n = 3; $n--;) {
    $status = Process::wait(true);
    echo "Recycled #{$status['pid']}, code={$status['code']}, signal={$status['signal']}" . PHP_EOL;
}

// wait 阻塞了，所以进程回收后才能继续执行
echo 'Parent #' . getmypid() . ' exit' . PHP_EOL;

