<?php
/**
 * User: zhan
 * Date: 2019/9/20
 * Email: <grianchan@gmail.com>
 */

namespace Zhan3333\RabbitMQ\Publishers;


use PhpAmqpLib\Message\AMQPMessage;
use Zhan3333\RabbitMQ\Exceptions\RabbitMQPublishException;
use Zhan3333\RabbitMQ\RabbitMQ;

/**
 * 生产者
 * Class Publisher
 * @package Zhan3333\RabbitMQ\Publishers
 */
class Publisher extends RabbitMQ
{
    /**
     * 推送消息
     * @param array|object|string $message
     * @return bool
     */
    public function publish($message)
    {
        $this->logger->debug('Publisher start', [$message]);
        if (empty($message)) {
            throw new RabbitMQPublishException("Publish message can't be $message");
        }
        try {
            if (is_array($message) || is_object($message)) {
                $message = json_encode($message);
            }
            // 创建消息
            $amqpMsgObj = new AMQPMessage($message, ['content_type' => 'text/plain', 'delivery_mode' => 2]);

            // 推送消息
            $this->channel->basic_publish($amqpMsgObj, $this->config['exchange_name'], $this->config['routing_key']);
            return true;
        } catch (\Exception $exception) {
            throw new RabbitMQPublishException('Publish message failed', 0, $exception);
        }
    }

    public function __destruct()
    {
        $this->logger->info('Publisher end');
        parent::__destruct();
    }
}
