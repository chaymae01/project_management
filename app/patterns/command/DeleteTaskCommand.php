<?php

namespace App\Patterns\Command;

use App\Models\Entities\Task;
use App\Repositories\TaskRepository;
use App\Services\TaskCompositeService;
use App\Patterns\Composite\ComplexTask;
class DeleteTaskCommand implements TaskCommand
{
    private $taskRepository;
    private $task;
    private $compositeService;

    public function __construct(TaskRepository $taskRepository, Task $task, TaskCompositeService $compositeService)
    {
        $this->taskRepository = $taskRepository;
        $this->task = $task;
        $this->compositeService = $compositeService;
    }

  public function execute()
{
    $descendants = $this->taskRepository->findTaskWithDescendants($this->task->id);

  
    foreach ($descendants as $descendant) {
        if ($descendant->getId() !== $this->task->id) {
            $this->taskRepository->delete($descendant->getId());
        }
    }


    $this->taskRepository->delete($this->task->id);
}


}
?>
