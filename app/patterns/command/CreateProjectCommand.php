<?php
namespace App\Patterns\Command;

use App\Models\Entities\Project;
use App\Services\ProjectService;

class CreateProjectCommand implements ProjectCommand
{
    private $projectService;
    private $project;
    private $creatorId;
    private $createdProjectId = null;

    public function __construct(ProjectService $projectService, Project $project, int $creatorId)
    {
        $this->projectService = $projectService;
        $this->project = $project;
        $this->creatorId = $creatorId;
    }

    public function execute(): array
    {
        $result = $this->projectService->createProject($this->project, $this->creatorId);
        if ($result['success'] && isset($result['project_id'])) {
            $this->createdProjectId = $result['project_id'];
        }
        return $result;
    }

}

