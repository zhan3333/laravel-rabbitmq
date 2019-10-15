<?php
/**
 * User: zhan
 * Date: 2019/9/20
 * Email: <grianchan@gmail.com>
 */
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
        'user' => env('TASK_RABBITMQ_USER', 'sync'),
        'pwd' => env('TASK_RABBITMQ_PWD', 'offlineDataSync'),
        'vhost' => env('TASK_RABBITMQ_VHOST', 'sync'),
        'exchange_name' => env('TASK_RABBITMQ_EXCHANGE_NAME', 'cass'),
        'queue_name' => env('TASK_RABBITMQ_QUEUE_NAME', 'task'),
        'routing_key' => env('TASK_RABBITMQ_ROUTING_KEY', 'cass_task'),
        // consumer only
        'consumer_tag' => env('TASK_RABBITMQ_CONSUMER_TAG', 'consumer_tag'),
    ],
];
