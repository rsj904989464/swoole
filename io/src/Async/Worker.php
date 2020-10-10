<?php
/**
 *  阻塞模型：一个连接未处理完成，新的连接不会处理
 */

namespace Rsj\Io\Async;

use Swoole\Event;

class  Worker
{
    public $onReceive = null;
    public $onConnect = null;
    public $onClose = null;
    public $socket = null;

    public function __construct($socket_address)
    {
        $this->socket = stream_socket_server($socket_address);
        echo $socket_address . "\n";
    }


    public function accept()
    {
        // 默认就是循环操作
        Event::add($this->socket, $this->create_socket());
    }

    public function create_socket()
    {
        return function ($socket) {
            $client = stream_socket_accept($this->socket);
            if (is_callable($this->onConnect)) {
                ($this->onConnect)($this, $client);
            }
            Event::add($client, $this->send_client());
        };
    }

    public function send_client()
    {
        return function ($socket) {
            $buffer = fread($socket, 1024);
            if (empty($buffer)) {
                if (feof($socket) || !is_resource($socket)) {
                    swoole_event_del($socket);
                    fclose($socket);
                }
            }
            if (!empty($buffer) && is_callable($this->onReceive)) {
                ($this->onReceive)($this, $socket, $buffer);
            }
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

    public function start()
    {
        $this->accept();
    }

}