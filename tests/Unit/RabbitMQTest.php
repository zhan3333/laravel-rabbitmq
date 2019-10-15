<?php
/**
 * User: zhan
 * Date: 2019/9/20
 * Email: <grianchan@gmail.com>
 */

namespace Tests\Unit;


use Tests\TestCase;
use Tests\Unit\Tasks\TaskDispatchExceptionTest;
use Tests\Unit\Tasks\TaskDispatchNowTest;
use Tests\Unit\Tasks\TaskDispatchTest;
use Tests\Unit\Tasks\TaskDispatchThrowableTest;
use Zhan3333\RabbitMQ\Publishers\TaskPublisher;

class RabbitMQTest extends TestCase
{
    /**
     * @test
     */
    public function sendMessage()
    {
        app(TaskPublisher::class)->publish('test message');
    }

    /**
     * @test
     */
    public function dispatchTask()
    {
        \Log::debug(__FUNCTION__ . ' ---------------');
        $taskId = TaskDispatchTest::dispatch(['dispatch task' => ['foo' => 'bar', true => false,]])->taskId;
        $this->assertInternalType('string', $taskId);

        $taskId = TaskDispatchTest::dispatchNow(['dispatch task now' => ['foo' => 'bar', true => false,]])->taskId;
        $this->assertInternalType('string', $taskId);

        $taskId = 'test task id111111111';
        TaskDispatchTest::dispatch(['dispatch task with set task' => ['foo' => 'bar', true => false,]])->setTaskId($taskId);
        $this->assertInternalType('string', $taskId);

        TaskDispatchTest::dispatchNow(['dispatch task now with set task' => ['foo' => 'bar', true => false,]])->setTaskId($taskId);
        $this->assertInternalType('string', $taskId);

        \Log::debug(__FUNCTION__ . ' ---------------');
    }

    /**
     * @test
     */
    public function dispatchNowTask()
    {
        \Log::debug(__FUNCTION__ . ' ---------------');
        $taskId = TaskDispatchTest::dispatchNow(['dispatch params' => ['foo' => 'bar', true => false,]])->taskId;
        $this->assertInternalType('string', $taskId);
        \Log::debug(__FUNCTION__ . ' ---------------');
    }

    /**
     * @test
     */
    public function dispatchTaskException()
    {
        \Log::debug(__FUNCTION__ . ' ---------------');
        $taskId = TaskDispatchExceptionTest::dispatch(['dispatch params' => ['foo' => 'bar', true => false,]])->taskId;
        $this->assertInternalType('string', $taskId);

        $taskId = TaskDispatchExceptionTest::dispatchNow(['dispatch params' => ['foo' => 'bar', true => false,]])->taskId;
        $this->assertInternalType('string', $taskId);
        \Log::debug(__FUNCTION__ . ' ---------------');
    }

    /**
     * @test
     */
    public function dispatchTaskThrowable()
    {
        \Log::debug(__FUNCTION__ . ' ---------------');
        $taskId = TaskDispatchThrowableTest::dispatch(['dispatch params' => ['foo' => 'bar', true => false,]])->taskId;
        $this->assertInternalType('string', $taskId);

        $taskId = TaskDispatchThrowableTest::dispatchNow(['dispatch now params' => ['foo' => 'bar', true => false,]])->taskId;
        $this->assertInternalType('string', $taskId);
        \Log::debug(__FUNCTION__ . ' ---------------');
    }
}
