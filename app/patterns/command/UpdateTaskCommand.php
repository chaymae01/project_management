<?php

namespace App\Patterns\Command;

use App\Models\Entities\Task;
use App\Patterns\State\TaskState;
use App\Repositories\TaskRepository;
use App\Patterns\Composite\ComplexTask;
use App\Services\TaskCompositeService;

class UpdateTaskCommand implements TaskCommand
{
    private $taskRepository;
    private $task;
    private $newState;
    private $compositeService;

    public function __construct(TaskRepository $taskRepository, Task $task, TaskState $newState, TaskCompositeService $compositeService)
    {
        $this->taskRepository = $taskRepository;
        $this->task = $task;
        $this->newState = $newState;
    }

   public function execute()
{
    $this->task->setState($this->newState);
    $this->task->status = $this->newState->getName();

    return $this->taskRepository->update($this->task);
}

   
    }
    ?>
