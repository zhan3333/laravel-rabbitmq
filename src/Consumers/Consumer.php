<?php
/**
 * User: zhan
 * Date: 2019/9/20
 * Email: <grianchan@gmail.com>
 */

namespace Zhan3333\RabbitMQ\Consumers;

use PhpAmqpLib\Message\AMQPMessage;
use Zhan3333\RabbitMQ\RabbitMQ;

/**
 * 消费者基类
 * Class Consumer
 */
class Consumer extends RabbitMQ
{
    public function process_message(AMQPMessage $message)
    {
        $this->logger->debug('Receive message', [$message->getBody()]);
        $this->channel->basic_ack($message->getDeliveryTag());
    }

    /**
     * 启动
     */
    public function start()
    {
        $this->logger->debug('Receive process start');
        $this->channel->basic_consume($this->config['queue_name'], $this->config['consumer_tag'], false, false, false, false, [$this, 'process_message']);
        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    public function __destruct()
    {
        $this->logger->info('Receive process stop');
        parent::__destruct();
    }
}
