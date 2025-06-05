<?php
namespace App\Patterns\Factory;

use App\Models\Entities\Project;

class KanbanProjectFactory implements IProjectFactory
{
    public function createProject(string $name, string $description, string $start_date, string $end_date, int $creator_id): Project
    {
        $project = new Project($name, $description, $start_date, $end_date, $creator_id, 'kanban');
        $project->setBoard([
            'To Do' => ['status' => 'todo', 'wip_limit' => null],
            'In Progress' => ['status' => 'in_progress', 'wip_limit' => 5],
            'Done' => ['status' => 'done', 'wip_limit' => null]
        ]);
        return $project;
    }
}