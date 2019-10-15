<?php
/**
 * User: zhan
 * Date: 2019/9/23
 * Email: <grianchan@gmail.com>
 */

namespace Zhan3333\RabbitMQ\Process;


use Swoole\Process;
use Zhan3333\RabbitMQ\Consumers\TaskConsumer;

class RabbitMQTaskConsumerProcessManager
{
    public $mpid = 0;
    public $works = [];
    public $max_process = 1;
    public $new_index = 0;
    /** @var Pid file path */
    private $pid_file;
    /** @var string */
    private $master_name;
    /** @var bool|mixed 是否开启守护进程 */
    private $daemon = false;

    public function __construct($config)
    {
        $this->master_name = $config['name'] ?? 'php-ps:master';
        $this->max_process = $config['max_process'] ?? 1;
        $this->daemon = $config['daemon'] ?? false;
        $this->pid_file = $config['pid_file'] ?? storage_path("pids/{$this->master_name}.pid");
    }

    public function start()
    {
        try {
            swoole_set_process_name($this->master_name);
            if ($this->daemon) {
                Process::daemon(true, true);
            }
            $this->mpid = posix_getpid();
            $this->run();
            $this->processWait();
        } catch (\Exception $e) {
            die('ALL ERROR: ' . $e->getMessage());
        }
    }

    public function stop(): void
    {
        Process::kill($this->getPid());
    }

    public function getStatus()
    {
        return $this->isRun() ? 'run' : 'stop';
    }

    public function getName()
    {
        return $this->master_name;
    }

    public function isRun()
    {
        $pid = $this->getPid();
        if ($pid) {
            return Process::kill($pid, 0);
        }
        return false;
    }

    public function getPid(): int
    {
        $pid = null;
        // try get pid from pid_file
        if (file_exists($this->pid_file)) {
            $pid = file_get_contents($this->pid_file);
        }
        // try get pid from pidof
        if (!$pid) {
            $pid = @shell_exec("pidof {$this->master_name}");
            if ($pid) {
                $pid = str_replace("\n", '', $pid);
            }
        }
        return (integer)$pid;
    }

    private function run()
    {
        for ($i = 0; $i < $this->max_process; $i++) {
            $this->CreateProcess();
        }
    }

    private function CreateProcess($index = null)
    {
        $process = new Process(function (Process $worker) use ($index) {
            if ($index === null) {
                $this->new_index++;
            }
            swoole_set_process_name($this->master_name . '-child');
            app(TaskConsumer::class)->start();
        }, false, false);
        $pid = $process->start();
        $this->works[$index] = $pid;
        return $pid;
    }

    private function checkMpid(&$worker)
    {
        if (!Process::kill($this->mpid, 0)) {
            $worker->exit();
            // 这句提示,实际是看不到的.需要写到日志中
            echo "Master process exited, I [{$worker['pid']}] also quit\n";
        }
    }

    private function rebootProcess($ret)
    {
        $pid = $ret['pid'];
        $index = array_search($pid, $this->works);
        if ($index !== false) {
            $index = intval($index);
            $new_pid = $this->CreateProcess($index);
            echo "rebootProcess: {$index}={$new_pid} Done\n";
            return;
        }
        throw new \Exception('rebootProcess Error: no pid');
    }

    private function processWait()
    {
        while (1) {
            if (count($this->works)) {
                $ret = Process::wait();
                if ($ret) {
                    $this->rebootProcess($ret);
                }
            } else {
                break;
            }
        }
    }
}
