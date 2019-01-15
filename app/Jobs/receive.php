<?php
/**
 * Created by PhpStorm.
 * User: HASEE
 * Date: 2018/12/31
 * Time: 1:34
 */
require_once __DIR__ . '/../../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

//队列名称
//$queue_name = 'logs';

//临时队列 断开连接就会删除
list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);

//交换机
$exchange = 'direct_logs';
//绑定路由
$binding_key = 'black';

//$channel->queue_declare('hello', false, true, false, false);
$channel->exchange_declare($exchange, 'direct', false, false, false);

echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

$callback = function($msg){
    echo " [x] Received ", $msg->body, "\n";
    sleep(substr_count($msg->body, '.'));
    echo " [x] Done", "\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};
//负载均衡 这告诉 RabbitMQ 一次不要向工作人员发送多个消息。 或者换句话说，不要向工作人员发送新消息，直到它处理并确认了前一个消息。 相反，它会将其分派给不是仍然忙碌的下一个工作人员。
$channel->basic_qos(null, 1, null);

$channel->queue_bind($queue_name, $exchange, $binding_key);

$channel->basic_consume($queue_name, '', false, false, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();