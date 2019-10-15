<?php
/**
 * User: zhan
 * Date: 2019/9/20
 * Email: <grianchan@gmail.com>
 */

namespace Zhan3333\RabbitMQ\Commands;


use Illuminate\Console\Command;
use Zhan3333\RabbitMQ\Consumers\Consumer;

class RabbitMQConsumerCommand extends Command
{
    protected $signature = 'rabbitmq:consumer
    {action=start : 操作动作}
    {--config=task : 使用配置名称}
    ';
    protected $description = 'RabbitMQ 消费者 相关命令';

    public function handle()
    {
        $action = $this->argument('action');
        $config = $this->option('config');
        $consumer = app(Consumer::class, ['name' => $config]);
        if ($action === 'start') {
            $consumer->start();
        }
    }
}
