<?php
namespace App\Patterns\Command;

use App\Services\ProjectService;
use App\Models\Entities\Project; 

class DeleteProjectCommand implements ProjectCommand
{
    private $projectService;
    private $projectId;
    private $deletedProjectData = null; 

    public function __construct(ProjectService $projectService, int $projectId)
    {
        $this->projectService = $projectService;
        $this->projectId = $projectId;
    }

    public function execute(): array
    {
        $this->deletedProjectData = $this->projectService->getProjectById($this->projectId);

        if (!$this->deletedProjectData) {
            return ["success" => false, "error" => "Project not found, cannot delete or prepare undo."];
        }

        $result = $this->projectService->deleteProject($this->projectId);

        if (!$result['success']) {
            $this->deletedProjectData = null;
        }

        return $result;
    }

  
}

