<?php
/**
 *  阻塞模型：一个连接未处理完成，新的连接不会处理
 */

namespace Rsj\Io\MultiThreadBlocking;
class  Worker
{
    public $onReceive = null;
    public $onConnect = null;
    public $onClose = null;
    public $socket = null;
    protected $config = [
        'workerNum' => 4,
    ];

    public function __construct($socket_address)
    {
        $this->socket = stream_socket_server($socket_address);
        echo $socket_address . "\n";
    }

    function fork()
    {
        for ($i = 0; $i < $this->config['workerNum']; $i++) {
            // 创建子进程
            $son_pid = \pcntl_fork();
            if ($son_pid > 0) {
                // 父进程空间
            } else if ($son_pid < 0) {
                $this->send($this->socket, "服务器异常");
            } else {
                echo $son_pid . "\n";
                // 由子进程完成事情
                $this->accept();
            }
        }

        if($son_pid){
            $a = \pcntl_wait($status);
            echo $a;
        }
    }


    public function accept()
    {
        // 接收连接和处理使用
        while (true) {
            $client = @stream_socket_accept($this->socket, 300);//等待客户端连接的时间
            if (!$client) die("null client to connect \n");

            echo posix_getpid()."\n";

            if (is_callable($this->onConnect)) {
                // 执行函数
                ($this->onConnect)($this, $client);
            }

            $data = fread($client, 65535);

            if (is_callable($this->onReceive)) {
                ($this->onReceive)($this, $client, $data);
            }
            // 处理完成之后关闭连接
            fclose($client);
        }
    }


    //发送信息
    public function send($client, $data)
    {
        $response = "HTTP/1.1 200 OK\r\n";
        $response .= "Content-Type: text/html;charset=UTF-8\r\n";
        $response .= "Connection: keep-alive\r\n";
        $response .= "Content-length: " . strlen($data) . "\r\n\r\n";
        $response .= $data;
        fwrite($client, $response);
    }

    public function start()
    {
        $this->fork();
    }

}