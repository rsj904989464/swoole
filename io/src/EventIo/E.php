<?php
use \Event as Event;

class E
{
    protected $client;
    protected $eventBase;

    function __construct($eventBase, $client, &$count)
    {
        $this->eventBase = $eventBase;
        $this->client = $client;
    }

    public function handler()
    {
        $event = new \Event($this->eventBase, $this->client, \Event::PERSIST | \Event::READ | \Event::WRITE, function ($socket) {
            // 对于建立处理时间
            var_dump(fread($socket, 65535));
            fwrite($socket, " 提前祝大家平安夜快乐 \n");
            fclose($socket);

            //移除事件，socket已经关闭，如果不移除，此事件会阻塞
            ($this->count[(int)$socket][\Event::PERSIST | \Event::READ | \Event::WRITE])->free();
        });
        $event->add();
        $this->count[(int)$this->client][\Event::PERSIST | \Event::READ | \Event::WRITE] = $event;
        var_dump($this->count);
    }
}