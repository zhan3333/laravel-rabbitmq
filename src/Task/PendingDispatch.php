<?php


namespace Zhan3333\RabbitMQ\Task;


use Ramsey\Uuid\Uuid;
use Zhan3333\RabbitMQ\Publishers\TaskPublisher;

class PendingDispatch
{
    /**
     * @var string job 的唯一id, finish回得到结果
     */
    public $taskId;

    /** @var Job $task */
    public $task;

    private $dispatchNow = false;

    public function __construct(Task $task, $setting = [])
    {
        $this->dispatchNow = $setting['dispatch_now'] ?? false;
        $this->task = $task;
        $this->setTaskId(Uuid::uuid4()->toString());
    }

    public function setTaskId($taskId)
    {
        $this->taskId = $taskId;
        $this->task->taskId = $taskId;
    }

    public function dispatch()
    {
        app(TaskPublisher::class)->publish($this->getPayload());
    }

    public function dispatchNow()
    {
        try {
            $result = $this->task->handle(...Inject::getInjectParams($this->task, 'handle'));
            if (method_exists($this->task, 'finish')) {
                $this->task->finish($this->taskId, $result);
            }
        } catch (\Throwable $exception) {
            $this->fail($exception);
        }
    }

    private function fail(\Throwable $exception)
    {
        if (method_exists($this->task, 'failed')) {
            $this->task->failed($this->taskId, $exception);
        }
    }

    /**
     * 发送到task的data数据
     * @return array
     * @throws \ReflectionException
     */
    public function getPayLoad(): array
    {
        return [
            'id' => $this->taskId,
            'task' => [
                get_class($this->task),
                'handle',
            ],
            'data' => $this->getTaskProperties(),
        ];
    }

    /**
     * 获取Job中的所有属性的 key=>value
     * @return array
     * @throws \ReflectionException
     */
    private function getTaskProperties(): array
    {
        $class = new \ReflectionClass($this->task);
        $exceptNames = $this->task->exceptParamNames;
        $properties = $class->getProperties();
        $data = [];
        foreach ($properties as $property) {
            $name = $property->getName();
            if (!in_array($name, $exceptNames, true)) {
                $data[$name] = $this->task->{$name};
            }
        }
        return $data;
    }

    public function __destruct()
    {
        if ($this->dispatchNow) {
            $this->dispatchNow();
        } else {
            $this->dispatch();
        }
    }
}
