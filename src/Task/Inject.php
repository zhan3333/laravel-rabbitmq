<?php


namespace Zhan3333\RabbitMQ\Task;


class Inject
{
    public static function getInjectParams($class, $method): array
    {
        $relMethod = new \ReflectionMethod($class, $method);
        // handle 函数依赖注入
        $methodParams = $relMethod->getParameters();
        foreach ($methodParams as $key => $param) {
            if ($param->getType()) {
                $methodParams[$key] = app($param->getType()->getName());
            } else {
                $methodParams[$key] = null;
            }
        }
        return $methodParams;
    }

    public static function fullConstructParams($class, $values): array
    {
        $relClass = new \ReflectionClass($class);
        if ($constructor = $relClass->getConstructor()) {
            // 类如果有 constructor ,则从values中取值
            $params = $constructor->getParameters();
            $constructorParams = [];
            foreach ($params as $key => $param) {
                $constructorParams[$key] = $values[$param->getName()] ?? null;
            }
            return $constructorParams;
        }
        return [];
    }
}
