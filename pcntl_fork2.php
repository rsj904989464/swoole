<?php

for ($i = 0; $i < 4; $i++) {
    $son_pid = pcntl_fork();
    if ($son_pid > 0) {

    } else if ($son_pid < 0) {

    } else {

    }


    // 父进程监听子进程情况并回收进程
    if ($son_pid) {
        $status = 0;
        $sop = \pcntl_wait($status);
        echo $sop . "\n";
    }
}
