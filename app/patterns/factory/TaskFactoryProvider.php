<?php
namespace App\Patterns\Factory;

class TaskFactoryProvider
{
    public static function getFactory(string $taskType): ITaskFactory
    {
        return match ($taskType) {
            'complex' => new ComplexTaskFactory(),
            default   => new StandardTaskFactory(),
        };
    }
}