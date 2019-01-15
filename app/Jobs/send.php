<?php
/**
 * Created by PhpStorm.
 * User: HASEE
 * Date: 2018/12/31
 * Time: 1:25
 */
require_once __DIR__ . '/../../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

//队列名称
$queue_name = 'logs';
//交换机
$exchange = 'direct_logs';
//绑定路由
$binding_key = 'black';

//$channel->queue_declare('hello', false, true, false, false);
$channel->exchange_declare($exchange, 'direct', false, false, false);

$data = implode(' ', array_slice($argv, 1));
if(empty($data)) $data = "Hello World!";
$msg = new AMQPMessage($data,array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));


//$channel->basic_publish($msg, '', 'hello');
//$channel->queue_bind($queue_name, $exchang,$binding_key);
for($i=0; $i<100; $i++){
    $channel->basic_publish($msg, $exchange,$binding_key);
}

echo " [x] Sent ", $data, "\n";

$channel->close();
$connection->close();