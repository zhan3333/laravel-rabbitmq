<?php
/**
 * User: zhan
 * Date: 2019/9/20
 * Email: <grianchan@gmail.com>
 */

namespace Zhan3333\RabbitMQ\Commands;


use Illuminate\Console\Command;
use Zhan3333\RabbitMQ\Consumers\Consumer;
use Zhan3333\RabbitMQ\Publishers\Publisher;

class RabbitMQPublisherCommand extends Command
{
    protected $signature = 'rabbitmq:publisher
    {action=send : 操作动作}
    {--config=task : 使用配置名称}
    {--message= : 发送消息文本, 字符串或者json_encode 后的数组对象}
    ';
    protected $description = 'RabbitMQ 消费者 相关命令';

    public function handle()
    {
        $action = $this->argument('action');
        $config = $this->option('config');
        $message = $this->option('message');
        $publisher = app(Publisher::class, ['name' => $config]);
        if ($action === 'send') {
            $publisher->publish($message);
        }
    }
}
