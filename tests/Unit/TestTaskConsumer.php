<?php


namespace Tests\Unit;


use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Tests\Unit\Tasks\TaskDispatchTest;
use Zhan3333\RabbitMQ\Consumers\TaskConsumer;

class TestTaskConsumer extends TestCase
{
    public function testCallStart()
    {
        $this->assertTrue(true);
        app(TaskConsumer::class)
            ->after(function ($id, $task, $data) {
                dump('after');
                Log::debug('after', [$id, $task, $data]);
            })
            ->before(function ($id, $task, $data) {
                dump('before');
                Log::debug('befor', [$id, $task, $data]);
            })
            ->start();
    }

    public function testPushTask()
    {
        $taskId = TaskDispatchTest::dispatch(['dispatch task' => ['foo' => 'bar', true => false,]])->taskId;
    }
}
