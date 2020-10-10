<?php
namespace Rsj\Io\Multi;
class  Worker
{
    public $onReceive = null;
    public $onConnect = null;
    public $onClose = null;
    public $socket = null;
    public $sockets = [];

    public function __construct($socket_address)
    {
        $this->socket = stream_socket_server($socket_address);
        stream_set_blocking($this->socket,0);
        $this->sockets[(int)$this->socket] = $this->socket;
        echo $socket_address . "\n";
    }


    public function accept()
    {
        // 接收连接和处理使用
        $i=1;
        while (true) {
            $read = $this->sockets;
            stream_select($read,$w,$e,1);
            echo ++$i.':'.count($read)."\n";
            foreach($read as $socket){
                if($socket == $this->socket){
                    $this->create_socket();
                }else{
//                    $this->sendMsg($socket);
                }
            }

        }
    }

    public function create_socket(){
        $client = stream_socket_accept($this->socket);
        if(is_callable($this->onConnect)){
            ($this->onConnect)($this->socket,$client);
        }
        $this->sockets[(int)$client] = $client;
    }

    //发送信息
    public function sendMsg($client)
    {
       $data = fread($client,65535);
       if(!$data) return null;
        if(is_callable($this->onReceive)){
            ($this->onReceive)($this,$client,$data);
        }
    }

    public function send($client,$data){
        fwrite($client,$data);
    }

    public function start(){
        $this->accept();
    }

}