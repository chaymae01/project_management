<?php
namespace App\Services;

use App\Patterns\Composite\{SimpleTask, ComplexTask, TaskComponent};
use App\Models\Entities\Task as TaskEntity;
use App\Repositories\TaskRepository;

class TaskCompositeService
{
    private TaskRepository $repository;

    public function __construct(TaskRepository $repository)
    {
        $this->repository = $repository;
    }

  
    public function buildTaskTree(int $projectId, ?string $status = null, ?int $rootParentId = null): array
    {
        $tasks = $this->repository->getTasksByProjectIdFiltered($projectId, $status, null); 

        if (empty($tasks)) {
            return [];
        }

        $taskMap = [];
        $rootTasks = [];

        foreach ($tasks as $taskEntity) {
            $taskComponent = $taskEntity->task_type === 'complex'
                ? new ComplexTask($taskEntity)
                : new SimpleTask($taskEntity);
            $taskMap[$taskComponent->getId()] = $taskComponent;
        }

        foreach ($taskMap as $taskId => $taskComponent) {
            $parentId = $taskComponent->getEntity()->parent_id;

            if ($parentId !== null && isset($taskMap[$parentId])) {
                 if ($taskMap[$parentId] instanceof ComplexTask) {
                    $taskMap[$parentId]->addChild($taskComponent);
                 } else {
                 }
            } elseif ($parentId === null) {
               
                if ($rootParentId === null || $parentId === $rootParentId) {
                     $rootTasks[] = $taskComponent;
                }
            }
        }
        
       
        if ($rootParentId !== null) {
             $rootTasks = array_filter($rootTasks, fn($task) => $task->getEntity()->parent_id === $rootParentId);
        }

        return $rootTasks;
    }

    public function getTaskWithChildren(int $taskId): ?TaskComponent
    {
        $tasks = $this->repository->findTaskWithDescendants($taskId);

        if (empty($tasks)) {
            return null;
        }

        $taskMap = [];

        foreach ($tasks as $taskEntity) {
            $taskComponent = $taskEntity->task_type === 'complex'
                ? new ComplexTask($taskEntity)
                : new SimpleTask($taskEntity);
            $taskMap[$taskComponent->getId()] = $taskComponent;
        }

        $rootComponent = null;
        foreach ($taskMap as $currentTaskId => $taskComponent) {
             if ($currentTaskId === $taskId) {
                 $rootComponent = $taskComponent;
             }

            $parentId = $taskComponent->getEntity()->parent_id;

            if ($parentId !== null && isset($taskMap[$parentId])) {
                 if ($taskMap[$parentId] instanceof ComplexTask) {
                    $taskMap[$parentId]->addChild($taskComponent);
                 } else {
                 }
            }
        }

        return $rootComponent; 
    }

    public function getSubtaskCount(int $taskId): int
    {
        
        return $this->repository->countDirectChildren($taskId);
    }
}

