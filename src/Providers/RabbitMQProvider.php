<?php
/**
 * User: zhan
 * Date: 2019/9/20
 * Email: <grianchan@gmail.com>
 */

namespace Zhan3333\RabbitMQ\Providers;

use Illuminate\Support\ServiceProvider;
use Zhan3333\RabbitMQ\Commands\RabbitMQConsumerCommand;
use Zhan3333\RabbitMQ\Commands\RabbitMQPublisherCommand;
use Zhan3333\RabbitMQ\Commands\RabbitMQTaskConsumerCommand;
use Zhan3333\RabbitMQ\Consumers\Consumer;
use Zhan3333\RabbitMQ\Consumers\TaskConsumer;
use Zhan3333\RabbitMQ\Exceptions\RabbitMQConfigException;
use Zhan3333\RabbitMQ\Publishers\Publisher;
use Zhan3333\RabbitMQ\Publishers\TaskPublisher;
use Zhan3333\RabbitMQ\RabbitMQLogger;


class RabbitMQProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                RabbitMQConsumerCommand::class,
                RabbitMQPublisherCommand::class,
                RabbitMQTaskConsumerCommand::class,
            ]);
        }
        $this->publishes([
            __DIR__ . '/../../config/config.php' => config_path('rabbitmq.php'),
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/config.php', 'rabbitmq'
        );

        $this->app->singleton(Consumer::class, function ($app, $options) {
            if (empty($options['name'])) {
                throw new RabbitMQConfigException('Rabbit use consumer must require name');
            }
            return new Consumer($options['name'], config("rabbitmq.{$options['name']}"));
        });
        $this->app->singleton(TaskConsumer::class, function () {
            return new TaskConsumer('task', config('rabbitmq.task'));
        });
        $this->app->singleton(Publisher::class, function ($app, $options) {
            if (empty($options['name'])) {
                throw new RabbitMQConfigException('Rabbit use publisher must require name');
            }
            return new Publisher($options['name'], config("rabbitmq.{$options['name']}"));
        });
        $this->app->singleton(TaskPublisher::class, function () {
            return new TaskPublisher('task', config('rabbitmq.task'));
        });
        $this->app->singleton(RabbitMQLogger::class, function ($app, $options) {
            if ($options['name'] && config("rabbitmq.{$options['name']}.log_enable", false)) {
                return new RabbitMQLogger($app['log']->channel(config("rabbitmq.{$options['name']}.log_channel", config('logging.default'))));
            }
            return new RabbitMQLogger();
        });
    }
}
