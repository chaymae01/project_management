<?php

namespace App\Patterns\Command;

use App\Models\Entities\Task;
use App\Repositories\TaskRepository;
use App\Patterns\State\TaskStateContext;
use App\Patterns\State\TodoState;
use App\Patterns\State\InProgressState;
use App\Patterns\State\DoneState;

class MoveTaskCommand implements TaskCommand
{
    private $taskRepository;
    private $task;
    private $oldStatus;
    private $newStatus;

    public function __construct(TaskRepository $taskRepository, Task $task, string $newStatus)
    {
        $this->taskRepository = $taskRepository;
        $this->task = $task;
        $this->oldStatus = $task->status; 
        $this->newStatus = $newStatus;
    }

    public function execute()
    {
        $stateContext = new TaskStateContext($this->task);
        $transitionMethod = 'transitionTo' . str_replace(' ', '', $this->newStatus);

        if (!method_exists($stateContext, $transitionMethod)) {
            throw new \Exception("Transition non implémentée");
        }

        $stateContext->$transitionMethod();
        $this->task->status = $this->newStatus;
        return $this->taskRepository->update($this->task);
    }

  
}
?>
