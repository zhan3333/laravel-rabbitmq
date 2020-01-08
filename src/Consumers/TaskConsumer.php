<?php
/**
 * User: zhan
 * Date: 2019/9/20
 * Email: <grianchan@gmail.com>
 */

namespace Zhan3333\RabbitMQ\Consumers;

use PhpAmqpLib\Message\AMQPMessage;
use Zhan3333\RabbitMQ\RabbitMQ;
use Zhan3333\RabbitMQ\Task\Inject;

/**
 * 任务消费者基类
 * Class TaskConsumer
 */
class TaskConsumer extends RabbitMQ
{
    public $beforeCallbacks = [];
    public $afterCallbacks = [];

    public function before(\Closure $callback)
    {
        $this->beforeCallbacks[] = $callback;
        return $this;
    }

    public function after(\Closure $callback)
    {
        $this->afterCallbacks[] = $callback;
        return $this;
    }

    private function callBefore($id, $task, $data)
    {
        foreach ($this->beforeCallbacks as $beforeCallback) {
            $beforeCallback($id, $task, $data);
        }
    }

    private function callAfter($id, $task, $data, $result)
    {
        foreach ($this->afterCallbacks as $afterCallback) {
            $afterCallback($id, $task, $data, $result);
        }
    }

    public function onMessage(AMQPMessage $message)
    {
        $this->logger->debug('Receive message', [$message->getBody()]);
        $this->channel->basic_ack($message->getDeliveryTag());

        $payload = json_decode($message->getBody(), true);

        if (empty($payload['id']) || empty($payload['task'] || empty($payload['data']))) {
            return;
        }
        $this->callBefore($payload['id'], $payload['task'], $payload['data']['data'] ?? null);
        $taskId = $payload['id'];
        [$class, $method] = $payload['task'];
        $instant = new $class(...Inject::fullConstructParams($class, $payload['data']));
        $instant->taskId = $taskId;

        try {
            $result = $instant->{$method}(...Inject::getInjectParams($class, $method));
            $this->callAfter($payload['id'], $payload['task'], $payload['data'], $result);
            if (method_exists($instant, 'finish')) {
                $instant->finish($taskId, $result);
            }
        } catch (\Throwable $exception) {
            $this->callAfter($payload['id'], $payload['task'], $payload['data']['data'] ?? null, $exception);
            // todo 写到task失败表
            if (method_exists($instant, 'failed')) {
                $instant->failed($taskId, $exception);
            }
        }
    }

    /**
     * 启动
     */
    public function start()
    {
        $this->logger->debug('Receive process start');
        $this->channel->basic_consume($this->config['queue_name'], $this->config['consumer_tag'], false, false, false, false, [$this, 'onMessage']);
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
