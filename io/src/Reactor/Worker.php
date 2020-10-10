<?php
/**
 *  阻塞模型：一个连接未处理完成，新的连接不会处理
 */

namespace Rsj\Io\Reactor;

use Swoole\Event;

class  Worker
{
    public $onReceive = null;
    public $onConnect = null;
    public $onClose = null;
    public $socket = null;
    protected $socket_address = null;
    protected $config = [
        'worker_num' => 4,
        'context' => [
            'socket' => [
                'backlog' => '102400',
            ],
        ]
    ];

    public function __construct($socket_address)
    {
        echo $this->socket_address = $socket_address . "\n";
    }

    function fork()
    {
        for ($i = 0; $i < $this->config['worker_num']; $i++) {
            $pid = pcntl_fork();
            if ($pid > 0) {
                $this->workerPids[] = $pid;
            } else if ($pid < 0) {

            } else {
                $this->accept();
                break;
            }
        }

        for ($i=0; $i < $this->config['worker_num']; $i++) {
            $status = 0;
            pcntl_wait($status);
            echo "回收：".posix_getpid()."\n";
        }

    }

    public function accept()
    {
        echo "子进程：".posix_getpid()."事件添加成功\n";
        Event::add($this->initServer(), $this->createSocket());
    }

    public function initServer()
    {
        $context = stream_context_create($this->config['context']);
        // 设置端口可以被多个进程重复的监听
        stream_context_set_option($context, 'socket', 'so_reuseport', 1);
        return stream_socket_server($this->socket_address, $errno, $errstr, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN, $context);
    }

    public function createSocket()
    {
        return function ($socket) {

            $client = @stream_socket_accept($socket, 300);//等待客户端连接的时间
            if (!$client) die("null client to connect \n");
            echo "有连接进入，当前进程：".posix_getpid()."\n";
            if (is_callable($this->onConnect)) {
                call_user_func($this->onConnect, $client);
            }
            Event::add($client, function ($socket) {
                //从连接当中读取客户端的内容
                $buffer = fread($socket, 1024);
                //如果数据为空， 或者为false,不是资源类型
                if (empty($buffer)) {
                    if (feof($socket) || !is_resource($socket)) {
                        // 触发关闭事件
//                        swoole_event_del($socket);
//                        fclose($socket);
                    }
                } elseif (is_callable($this->onReceive)) {
                    call_user_func($this->onReceive, $this, $socket, $buffer);
                }
            });
        };
    }



    public function start()
    {
        $this->fork();
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

}