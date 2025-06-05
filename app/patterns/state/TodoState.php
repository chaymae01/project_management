<?php

   namespace App\Patterns\State;

class TodoState implements TaskState
{
   public function canTransitionTo(TaskState $state): bool
{
    return true;
}
    public function apply($task): void
    {
        $task->status = 'To Do';
    }

    public function getName(): string
    {
        return 'To Do';
    }
     public function move($task): void
    {
        $task->status = 'To Do';
        $task->position = 0;
    }
}
    ?>
