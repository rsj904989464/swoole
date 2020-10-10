<?php
$curr_pid = posix_getpid();//获取当前的进程id
// 将当前进程的id写入文件中
echo '当前父进程的id： '.$curr_pid."\n"; // 1
// pcntl_fork 创建子进程
// 返回子进程的id
$son_pid = pcntl_fork();
echo '创建的子进程的id： '.$son_pid."\n"; // 2 0
echo '创建子进程之后当前的进程为： '.posix_getpid()."\n"; // 1 2

$son_pid = pcntl_fork();
echo '创建的子进程的id： '.$son_pid."\n"; // 3 4 0
echo '创建子进程之后当前的进程为： '.posix_getpid()."\n"; // 1 2 3
while (1){

}