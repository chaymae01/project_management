<?php

    namespace App\Patterns\Command;

    use App\Models\Entities\Task;
    use App\Repositories\TaskRepository;

    class CreateTaskCommand implements TaskCommand
    {
      private $taskRepository;
      private $task;

      public function __construct(TaskRepository $taskRepository, Task $task)
      {
        $this->taskRepository = $taskRepository;
        $this->task = $task;
      }

      public function execute()
      {
        return $this->taskRepository->create($this->task);
      }

    
    }
    ?>
