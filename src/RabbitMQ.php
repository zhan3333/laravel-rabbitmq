<?php
/**
 * User: zhan
 * Date: 2019/9/20
 * Email: <grianchan@gmail.com>
 */

namespace Zhan3333\RabbitMQ;


use Illuminate\Log\LogManager;
use Illuminate\Support\Arr;
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
            Arr::get($this->config, 'host'),
            Arr::get($this->config, 'port'),
            Arr::get($this->config, 'user'),
            Arr::get($this->config, 'pwd'),
            Arr::get($this->config, 'vhost'),
            Arr::get($this->config, 'insist'),
            Arr::get($this->config, 'login_method'),
            Arr::get($this->config, 'login_response'),
            Arr::get($this->config, 'locale'),
            Arr::get($this->config, 'connection_timeout'),
            Arr::get($this->config, 'read_write_timeout'),
            Arr::get($this->config, 'context'),
            Arr::get($this->config, 'keepalive'),
            Arr::get($this->config, 'heartbeat'),
            Arr::get($this->config, 'channel_rpc_timeout'),
            Arr::get($this->config, 'ssl_protocol')
        );
    }

    public function createChannle()
    {
        $queue = Arr::get($this->config, 'queue_name');
        $exchange = Arr::get($this->config, 'exchange_name');
        $routerKey = Arr::get($this->config, 'routing_key');
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
