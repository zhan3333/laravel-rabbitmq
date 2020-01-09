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
        'connection_timeout' => env('TASK_RABBITMQ_CONNECTION_TIMEOUT', 3),
        'read_write_timeout' => env('TASK_RABBITMQ_READ_WRITE_TIMEOUT', 3),
        'channel_rpc_timeout' => 0.0,
        'ssl_protocol' => null,
        'insist' => false,
        'login_method' => 'AMQPLAIN',
        'login_response' => null,
        'locale' => 'en_US',
        'context' => null,
    ],
];
