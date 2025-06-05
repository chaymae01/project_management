<?php

   namespace App\Patterns\State;

class InProgressState implements TaskState
{
  public function canTransitionTo(TaskState $state): bool
{
  
    return true;
}

    public function apply($task): void
    {
        $task->status = 'In Progress';
        $task->started_at = date('Y-m-d H:i:s');
    }

    public function getName(): string
    {
        return 'In Progress';
    }
    
    public function move($task): void
    {
        $task->status = 'In Progress';
        $task->started_at = date('Y-m-d H:i:s');
    }
}
    ?>
