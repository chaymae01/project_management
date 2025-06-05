<?php
namespace App\Patterns\Factory;

use App\Models\Entities\Task;

interface ITaskFactory 
{
    public function createTask(
        string $title,
        string $description,
        int $project_id,
        int $creator_id,
        ?int $assignee_id = null,  
         ?string $deadline = null
    ): Task;
}