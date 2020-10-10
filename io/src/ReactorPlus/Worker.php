<?php

namespace Rsj\Io\ReactorPlus;
use Rsj\Io\Util\Inotify;

class  Worker
{
    use socket;
    protected $config = [
        'watch_file' => false,
        'heartbeat_check_interval' => 3
    ];
    protected $socket_address = null;

    public function __construct($socket_address)
    {
        $this->config = array_merge($this->base_config, $this->config);
        $this->socket_address = $socket_address;
    }

    protected function watchEvent()
    {
        return function ($event) {
            $action = 'file:';
            switch ($event['mask']) {
                case IN_CREATE:
                    $action = 'IN_CREATE';
                    break;
                case IN_DELETE:
                    $action = 'IN_DELETE';
                    break;
                case IN_MODIFY:
                    $action = 'IN_MODIF';
                    break;
                case IN_MOVE:
                    $action = 'IN_MOVE';
                    break;
            }
//            view('worker reloaded by inotify2 :' . $action . " : " . $event['name']);dd

            $pids = $this->fget();
            $master_pid = array_shift($pids);
            view("master_pid :".$master_pid);
            posix_kill($master_pid, SIGUSR1);//向主进程传递重启信号
        };
    }


    public function start()
    {
        view($this->socket_address);
        $this->fput();

        if ($this->config['watch_file']) {
            $this->inotify = new Inotify(basePath(), $this->watchEvent());
            $this->inotify->start();
        }
        $this->fork();
        $this->monitorWorkersForLinux();
    }

    /**
     *  注意：如果先杀父进程，程序终止，子进程将杀不死
     * @param bool $master
     */
    public function stop($master = true)
    {
        $pids = $this->fget();
        $master_pid = array_shift($pids);
        foreach ($pids as $key => $pid) {
            posix_kill($pid, 9);
        }
        if ($master) {
            posix_kill($master_pid, 9);
        }
        $this->fput();
    }

    public function reload()
    {
        $this->stop(false);
        $this->fork();
    }


    public function set($data){
        foreach ($data as $k=>$v){
            $this->config[$k] = $v;
        }
    }

    /**    信号重启、关闭 */
    public function sigHandler($sig)
    {
        switch ($sig) {
            case SIGUSR1://10
                $this->reload();
                break;
            case SIGINT:
                $this->stop();
                break;
        }
    }

    public function monitorWorkersForLinux()
    {
        pcntl_signal(SIGUSR1, [$this, 'sigHandler'], false); //重启
        pcntl_signal(SIGINT, [$this, 'sigHandler'], false); //停止 ctrl+c对应此信号
        while (1) {
            \pcntl_signal_dispatch();
            \pcntl_wait($status);
            \pcntl_signal_dispatch();
        }
    }


}