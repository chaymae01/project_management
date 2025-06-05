<?php
namespace App\Patterns\Factory;

use App\Models\Entities\Task;
use App\Patterns\Composite\ComplexTask;

class ComplexTaskFactory implements ITaskFactory
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
        $task->setPriority('high'); 
        $task->setTaskType('complex');
        $task->setDeadline($deadline);
        return $task;
    }
}
