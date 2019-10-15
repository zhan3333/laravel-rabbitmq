<?php
/**
 * User: zhan
 * Date: 2019/9/20
 * Email: <grianchan@gmail.com>
 */

namespace Zhan3333\RabbitMQ\Task;

/**
 * Class Task
 * @package Zhan3333\RabbitMQ\Task
 * @method handle(...$args)
 * @method finish($taskId, $result): void
 * @method failed($taskId, $exception): void
 */
class Task
{
    /**
     * 唯一键, 只有在
     * @var string
     */
    public $taskId;

    /**
     * 不会加载到task data 中的属性
     * @var array
     */
    public $exceptParamNames = [
        'taskId',
        'exceptParamNames',
    ];
}
