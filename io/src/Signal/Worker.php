<?php
/**
 *  阻塞模型：一个连接未处理完成，新的连接不会处理
 */

namespace Rsj\Io\Signal;
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
        // 接收连接和处理使用
        while (true) {
            $client = @stream_socket_accept($this->socket,300);//等待客户端连接的时间
            if(!$client) die("null client to connect \n");

            pcntl_signal(SIGIO,$this->sig($client));//安装信号

            posix_kill(posix_getpid(),SIGIO); //给进程发信号

            pcntl_signal_dispatch();//调用

            // 处理完成之后关闭连接
            fclose($client);
        }
    }

    public function sig($client){
        return function ($sig) use($client){
            if (is_callable($this->onConnect)) {
                // 执行函数
                ($this->onConnect)($this, $client);
            }

            $data = fread($client, 65535);

            if (is_callable($this->onReceive)) {
                ($this->onReceive)($this, $client, $data);
            }
        };
    }


    //发送信息
    public function send($client, $data)
    {
        $response = "HTTP/1.1 200 OK\r\n";
        $response .= "Content-Type: text/html;charset=UTF-8\r\n";
        $response .= "Connection: keep-alive\r\n";
        $response .= "Content-length: ".strlen($data)."\r\n\r\n";
        $response .= $data;
        fwrite($client, $response);
    }

    public function start(){
        $this->accept();
    }

}