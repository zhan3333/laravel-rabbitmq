<?php
/**
 * User: zhan
 * Date: 2019/9/20
 * Email: <grianchan@gmail.com>
 */

namespace Zhan3333\RabbitMQ;


use Illuminate\Log\LogManager;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;

class RabbitMQ
{
    /** @var array */
    public $config;

    /** @var AMQPStreamConnection */
    public $connection;

    /** @var \PhpAmqpLib\Channel\AMQPChannel */
    public $channel;

    /** @var LogManager */
    public $logger;

    public function __construct($name, array $config)
    {
        $this->name = $name;
        $this->config = $config;
        $this->logger = app(RabbitMQLogger::class, ['name' => $name]);
        $this->createConnection();
        $this->createChannle();
    }

    public function createConnection()
    {
        $this->connection = new AMQPStreamConnection(
            $this->config['host'],
            $this->config['port'],
            $this->config['user'],
            $this->config['pwd'],
            $this->config['vhost']);
    }

    public function createChannle()
    {
        $queue = $this->config['queue_name'];
        $exchange = $this->config['exchange_name'];
        $routerKey = $this->config['routing_key'];
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare($queue, false, true, false, false);
        $this->channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);
        $this->channel->queue_bind($queue, $exchange, $routerKey);
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
