<?php
use Workerman\Worker;
use \Workerman\Lib\Timer;
require_once '../workerman/Autoloader.php';
require_once 'create.php';
// Create a Websocket server
$ws_worker = new Worker("websocket://121.42.144.9:4000");

// 4 processes
$ws_worker->count = 4;


// Emitted when new connection come
$ws_worker->onConnect = function($connection)
{
    echo "Connection opened\n";	
};

// Emitted when data received
$ws_worker->onMessage = function($connection, $data)
{
    receive_message($connection, $data);
};

// Emitted when connection closed
$ws_worker->onClose = function($connection)
{
    echo "Connection closed\n";
};

// Run worker
Worker::runAll();


?>
