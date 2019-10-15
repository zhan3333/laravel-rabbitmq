<?php


namespace Zhan3333\RabbitMQ\Task;

trait Dispatchable
{
    public static function dispatch(): PendingDispatch
    {
        return new PendingDispatch(new static(...func_get_args()));
    }

    public static function dispatchNow(): PendingDispatch
    {
        return new PendingDispatch(new static(...func_get_args()), ['dispatch_now' => true]);
    }
}
