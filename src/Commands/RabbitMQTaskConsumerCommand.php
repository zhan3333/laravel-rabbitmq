<?php
/**
 * User: zhan
 * Date: 2019/9/20
 * Email: <grianchan@gmail.com>
 */

namespace Zhan3333\RabbitMQ\Commands;


use Illuminate\Console\Command;
use Zhan3333\RabbitMQ\Consumers\TaskConsumer;
use Zhan3333\RabbitMQ\Process\RabbitMQTaskConsumerProcessManager;

class RabbitMQTaskConsumerCommand extends Command
{
    protected $signature = 'rabbitmq:task-consumer
    {action=status : 操作动作}
    ';
    protected $description = 'RabbitMQ 任务消费者 相关命令';

    public function handle()
    {
        $action = $this->argument('action');
        $manager = new RabbitMQTaskConsumerProcessManager(config('rabbitmq.task.consumer'));
        if ($action === 'start') {
            if ($manager->isRun()) {
                $this->warn($manager->getName() . ' is run');
            } else {
                $this->info('Start ...');
                $manager->start();
            }
        }
        if ($action === 'status') {
            $this->info('Status is ' . $manager->getStatus());
        }
        if ($action === 'stop') {
            if ($manager->isRun()) {
                $manager->stop();
                $this->info('Stop success');
            } else {
                $this->warn("{$manager->getName()} not run");
            }
        }
    }
}
