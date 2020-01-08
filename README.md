# RabbitMQ

在Laravel中提供生产者和消费者的使用类，提供task版本的生产者和消费者，并提供相关的Command

- PHP AMQPLIB 库: `https://github.com/php-amqplib/php-amqplib`
- RabbitMQ 中文: `https://github.com/mr-ping/RabbitMQ_into_Chinese`
- 建议使用Docker运行RabbitMQ服务： `https://github.com/zhan3333/rabbitmq-docker`

## 操作手册

### RabbitMQ Queue 队列使用

- 配置 config/rebbitmq.php

```php
<?php

return [
    'active' => env('RABBITMQ_ACTIVE', true),
    // 任务队列配置
    'task' => [
        // 是否开启日志 true/false
        'log_enable' => env('TASK_RABBITMQ_LOG_ENABLE', false),
        // 日志使用通道
        'log_channel' => env('TASK_RABBITMQ_LOG_CHANNEL', 'daily'),
        // 队列配置
        'host' => env('TASK_RABBITMQ_HOST', '127.0.0.1'),
        'port' => env('TASK_RABBITMQ_PORT', 5672),
        'user' => env('TASK_RABBITMQ_USER', 'user'),
        'pwd' => env('TASK_RABBITMQ_PWD', 'pwd'),
        'vhost' => env('TASK_RABBITMQ_VHOST', 'vhost'),
        'exchange_name' => env('TASK_RABBITMQ_EXCHANGE_NAME', 'exchange'),
        'queue_name' => env('TASK_RABBITMQ_QUEUE_NAME', 'task'),
        'routing_key' => env('TASK_RABBITMQ_ROUTING_KEY', 'task'),
        // consumer only
        'consumer_tag' => env('TASK_RABBITMQ_CONSUMER_TAG', 'consumer_tag'),

        'keepalive' => env('TASK_RABBITMQ_KEEPALIVE', false),
        'heartbeat' => env('TASK_RABBITMQ_HEARTBEAT', 0),
        'connection_timeout' => env('TASK_RABBITMQ_CONNECTION_TIMEOUT', 0),
        'read_write_timeout' => env('TASK_RABBITMQ_READ_WRITE_TIMEOUT', 0),
        'channel_rpc_timeout' => 0.0,
        'ssl_protocol' => null,
        'insist' => false,
        'login_method' => 'AMQPLAIN',
        'login_response' => null,
        'locale' => 'en_US',
        'context' => null,
    ],
];

```

- 启动队列消费者

```
php artisan rabbitmq:task-consumer
```

- 自定义启动消费者
```php
use Zhan3333\RabbitMQ\Consumers\TaskConsumer;

function () {
    app(TaskConsumer::class)
        ->after(function ($id, $task, $data) {
            Log::debug('after', [$id, $task, $data]);
        })
        ->before(function ($id, $task, $data) {
            Log::debug('befor', [$id, $task, $data]);
        })
        ->start();
}
```

- 命令行推送消息到队列(可选调试用)

message: 推送的消息字符串，可以为string (array,object需要json_encode)
config: 使用的 rabbitmq.php 配置中的配置项名称
 
```bash
php artisan rabbitmq:publisher --message=test --config=task
```

- 创建队列类

```php
<?php

use App\Exceptions\Handler;
use Zhan3333\RabbitMQ\Task\Dispatchable;
use Zhan3333\RabbitMQ\Task\Task;

class PrintDataTask extends Task
{
    use Dispatchable;

    public $data;

    /**
     * 使用时通过 construct 传入参数，并保存到类的成员变量中(public)
     * PrintDataTask constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * - 将在队列消费者中执行，支持依赖注入
     * - 返回的结果将作为 finish 的第二个result参数
     * - 在handle中产生或者抛出的异常将会传到 failed 中
     * @return array
     */
    public function handle()
    {
        \Log::debug(__CLASS__ . '\\' . __FUNCTION__, [$this->taskId, $this->data]);
        return [
            'handle result' => 'success',
            'data' => $this->data,
        ];
    }

    /**
     * handle处理完成后的处理
     * @param $taskId
     * @param $result
     */
    public function finish($taskId, $result): void
    {
        \Log::debug(__CLASS__ . '\\' . __FUNCTION__, [$taskId, $result]);
    }

    /**
     * 处理handle中的异常
     * @param $taskId
     * @param Throwable $exception
     */
    public function failed($taskId, \Throwable $exception): void
    {
        if ($exception instanceof \Exception) {
            // 可选， 打印错误到 laravel.log
            app(Handler::class)->report($exception);
        }
        \Log::debug(__CLASS__ . '\\' . __FUNCTION__, [$taskId, $exception->getMessage()]);
    }
}
```

- 发送task到队列中

```php
// 返回唯一任务id

// 发送到队列中执行
$taskId = TaskDispatchTest::dispatch(['dispatch params' => ['foo' => 'bar', true => false,]])->taskId;

// 同步执行，但是操作上和异步完全一致，也返回taskId
$taskId = TaskDispatchTest::dispatchNow(['dispatch params' => ['foo' => 'bar', true => false,]])->taskId;

// 使用自定义的taskId
$taskId = '123456-789123';
TaskDispatchTest::dispatch(['dispatch params' => ['foo' => 'bar', true => false,]])->setTaskId($taskId);

```
