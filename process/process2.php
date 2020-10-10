<?php
/**
 *  问题：子进程不写，$process->read()会阻塞
 */
use Swoole\Process;

for ($i=0; $i < 3; $i++) {
    $process = new Swoole\Process(function($process){
        // 进程空间
        echo '子：'.$process->read()."\n";
    }, false, true);
    $pid = $process->start();
    $process->write('hello'.$pid);
    swoole_event_add($process->pipe,function ($pipe) use($process){
        echo '父：'.$process->read()."\n";
    });
}


