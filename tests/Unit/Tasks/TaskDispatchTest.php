<?php
/**
 * User: zhan
 * Date: 2019/9/20
 * Email: <grianchan@gmail.com>
 */

namespace Tests\Unit\Tasks;


use App\Exceptions\Handler;
use Zhan3333\RabbitMQ\Task\Dispatchable;
use Zhan3333\RabbitMQ\Task\Task;

class TaskDispatchTest extends Task
{
    use Dispatchable;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        \Log::debug(__CLASS__ . '\\' . __FUNCTION__, [$this->taskId, $this->data]);
        return [
            'handle result' => 'success',
            'data' => $this->data,
        ];
    }

    public function finish($taskId, $result): void
    {
        \Log::debug(__CLASS__ . '\\' . __FUNCTION__, [$taskId, $result]);
    }

    public function failed($taskId, \Throwable $exception): void
    {
        if ($exception instanceof \Exception) {
            app(Handler::class)->report($exception);
        }
        \Log::debug(__CLASS__ . '\\' . __FUNCTION__, [$taskId, $exception->getMessage()]);
    }
}
