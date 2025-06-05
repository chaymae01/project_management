<?php

namespace App\Patterns\State;

class DoneState implements TaskState
{
   public function canTransitionTo(TaskState $state): bool
{

    return true;
}

    public function apply($task): void
    {
        $task->status = 'Done';
        $task->completed_at = date('Y-m-d H:i:s');
    }

    public function getName(): string
    {
        return 'Done';
    }
     public function move($task): void
    {
        $task->status = 'Done';
        $task->completed_at = date('Y-m-d H:i:s');
    }
}
    ?>
