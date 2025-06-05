<?php

namespace App\Services;

use App\Repositories\TaskRepository;
use App\Patterns\Strategy\SortStrategy; 

class TaskService
{
    private $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function getTaskRepository(): TaskRepository
    {
        return $this->taskRepository;
    }

    public function find(int $id)
    {
        return $this->taskRepository->findById($id);
    }

    public function sortTasks(array $tasks, SortStrategy $strategy): array
    {
        return $strategy->sort($tasks);
    }

    public function getAndSortProjectTasks(int $projectId, ?SortStrategy $strategy = null): array
    {
       
        $tasks = $this->taskRepository->getTasksByProjectIdFiltered($projectId, null, null);

        if ($strategy !== null) {
            $tasks = $this->sortTasks($tasks, $strategy);
        }

        return $tasks;
    }
    
}
?>
