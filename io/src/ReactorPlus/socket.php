<?php
namespace Rsj\Io\ReactorPlus;
use Swoole\Event;

trait socket{
    public $onReceive = null;
    public $onConnect = null;
    public $onClose = null;
    public $socket = null;
    protected $workerPath = __DIR__ . "/pid/wokerpids";
    protected $base_config = [
        'worker_num' => 4,
        'context' => [
            'socket' => [
                'backlog' => '102400',
            ],
        ]
    ];

    function fork()
    {
        $this->fput(posix_getpid());
        for ($i = 0; $i < $this->config['worker_num']; $i++) {
            $pid = pcntl_fork();
            if ($pid > 0) {
                $this->fput($pid);
            } else if ($pid < 0) {

            } else {
                echo posix_getpid()."\n";
                $this->accept();
                exit;
            }
        }

    }

    public function accept()
    {
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

    function fput($pid = '')
    {
        $data = $pid ? file_get_contents($this->workerPath) . '|' . $pid : '';
        file_put_contents($this->workerPath, $data);
    }

    function fget()
    {
        return array_filter(explode('|', file_get_contents($this->workerPath)));
    }
}