<?php
require __DIR__."/../../vendor/autoload.php";
use Rsj\Io\ReactorPlus\Worker;
$host = 'tcp://0.0.0.0:9999';
$server = new Worker($host);
$server->stop();
