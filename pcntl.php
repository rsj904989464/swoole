<?php
pcntl_signal(SIGIO,'sig');

function sig($sig){
    echo $sig."\n";
}

echo "start=====\n";

posix_kill(posix_getpid(),SIGIO);

//分发
pcntl_signal_dispatch();