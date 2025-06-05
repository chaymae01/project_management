<?php
namespace App\Patterns\Factory;

use App\Models\Entities\Task;

class StandardTaskFactory implements ITaskFactory
{
    public function createTask(
        string $title,
        string $description,
        int $project_id,
        int $creator_id,
        ?int $assignee_id = null,
        ?string $deadline = null
    ): Task {
        $task = new Task($title, $description, $project_id, $creator_id);
        $task->setStatus('To Do');
        $task->setAssigneeId($assignee_id);
        $task->setTaskType('standard');
        $task->setPriority('low'); 
        $task->setDeadline($deadline);
        return $task;
    }
}

