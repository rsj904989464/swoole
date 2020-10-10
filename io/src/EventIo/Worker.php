<?php
/**
 *  阻塞模型：一个连接未处理完成，新的连接不会处理
 */
namespace Rsj\Io\EventIo;
use Rsj\Io\EventIo\E;
use \Event as Event;
class  Worker
{
    public $onReceive = null;
    public $onConnect = null;
    public $onClose = null;
    public $socket = null;
    public $eventBase = null;

    public function __construct($socket_address)
    {
        $this->eventBase = new \EventBase();
        $this->socket = stream_socket_server($socket_address);
        echo $socket_address . "\n";
    }


    public function accept()
    {
        $count = [];
        $event = new \Event($this->eventBase,$this->socket,\Event::READ | \Event::PERSIST,function () use (&$count){
            $client = @stream_socket_accept($this->socket,3);//等待客户端连接的时间
            if(!$client) die("null client to connect \n");
            echo "连接 start \n";
            stream_set_blocking($client, false);

            (new E($this->eventBase,$client,$count))->handler();
        });
        $event->add();
        $count[(int) $this->socket][Event::PERSIST | Event::READ | Event::WRITE] = $event;
        $this->eventBase->loop();
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