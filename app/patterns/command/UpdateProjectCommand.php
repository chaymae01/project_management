<?php
namespace App\Patterns\Command;

use App\Models\Entities\Project; 
use App\Services\ProjectService;

class UpdateProjectCommand implements ProjectCommand
{
    private $projectService;
    private $projectId;
    private $name;
    private $description;
    private $startDate;
    private $endDate;
    private $type;
    private $oldProjectData = null; 

    public function __construct(
        ProjectService $projectService,
        int $projectId,
        string $name,
        string $description,
        string $startDate,
        string $endDate,
        string $type
    ) {
        $this->projectService = $projectService;
        $this->projectId = $projectId;
        $this->name = $name;
        $this->description = $description;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->type = $type;
    }

    public function execute(): array
    {
        $this->oldProjectData = $this->projectService->getProjectById($this->projectId);

        if (!$this->oldProjectData) {
            return ['success' => false, 'error' => 'Project not found, cannot update or prepare undo.'];
        }

        return $this->projectService->updateProject(
            $this->projectId,
            $this->name,
            $this->description,
            $this->startDate,
            $this->endDate,
            $this->type
        );
    }

  
}

