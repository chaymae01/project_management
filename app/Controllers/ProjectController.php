<?php
namespace App\Controllers;

use App\Patterns\Factory\ProjectFactory;
use App\Services\ProjectService;
use App\Services\TaskService;
use App\Repositories\TaskRepository;
use App\Repositories\ProjectRepository;
use App\Auth\AuthMiddleware;
use PDO;
use App\Patterns\Factory\IProjectFactory;
use App\Patterns\Factory\KanbanProjectFactory;

use App\Services\TaskCompositeService;
use App\Patterns\Command\CreateProjectCommand;
use App\Patterns\Command\DeleteProjectCommand;
use App\Patterns\Command\UpdateProjectCommand;
use App\Models\Entities\Project;
use App\Patterns\Observer\ProjectNotifier;
use App\Patterns\Observer\LogObserver;
use App\Patterns\Observer\UserNotificationObserver;
use App\Models\Entities\NotificationType;
use App\Patterns\Strategy\DeadlineSort; 
use App\Patterns\Strategy\PrioritySort; 
use App\Patterns\Strategy\SortStrategy; 

class ProjectController
{
    private $projectService;
    private $taskService;
    private $compositeService;

    public function __construct(ProjectService $projectService, TaskService $taskService, TaskCompositeService $compositeService)
    {
        $this->projectService = $projectService;
        $this->taskService = $taskService;
        $this->compositeService = $compositeService;
    }

    private function createProjectFactory(string $type): IProjectFactory
    {
        switch ($type) {
          
            case 'kanban':
            default:
                return new KanbanProjectFactory();
        }
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $type = $_POST['type'];
            $creator_id = $_SESSION['user_id'];

            $projectFactory = $this->createProjectFactory($type);
            $project = $projectFactory->createProject($name, $description, $start_date, $end_date, $creator_id);
            
            $command = new CreateProjectCommand($this->projectService, $project, $creator_id);
            $creationResult = $command->execute();

            if ($creationResult["success"] && isset($creationResult["project"])) {
                $project = $creationResult["project"];
                $projectNotifier = new ProjectNotifier();
                $projectNotifier->attach(new LogObserver());
                $projectNotifier->attach(new UserNotificationObserver());
                
                $projectNotifier->notify(
                    NotificationType::PROJECT_UPDATE,
                    [
                        'project' => $project,
                        'actor_user_id' => $_SESSION['user_id'],
                        'actor_name' => $_SESSION['username'],
                        'entity_type' => 'project',
                        'entity_id' => $project->id
                    ]
                );

                header("Location: /projet_java/app/Projects");
                exit;
            } else {
                $error = $creationResult['error'] ?? 'Unknown error occurred';
                include __DIR__ . '/../views/project/create.php';
            }
        } else {
            include __DIR__ . '/../views/project/create.php';
        }
    }

    public function show(int $id)
    {
        $userId = $_SESSION['user_id'] ?? null;
        $projectRepository = new ProjectRepository();
        
        if (!$projectRepository->isUserMember($id, $userId)) {
            echo "Vous n'avez pas accès à ce projet";
            exit;
        }

        $project = $this->projectService->getProjectById($id);
        $tasksByStatus = [
            'To Do' => [],
            'In Progress' => [],
            'Done' => []
        ];

        if ($project) {
            $sortStrategy = null;
            if (isset($_GET['sort'])) {
                switch ($_GET['sort']) {
                    case 'priority':
                        $sortStrategy = new PrioritySort();
                        break;
                    case 'deadline':
                      
                        $sortStrategy = new DeadlineSort();
                        break;
                }
            }

            $tasksByStatus['To Do'] = $this->compositeService->buildTaskTree($id, 'To Do');
            $tasksByStatus['In Progress'] = $this->compositeService->buildTaskTree($id, 'In Progress');
            $tasksByStatus['Done'] = $this->compositeService->buildTaskTree($id, 'Done');

            if ($sortStrategy !== null) {
             
                foreach ($tasksByStatus as $status => $taskList) {
                  
                    $tasksByStatus[$status] = $this->taskService->sortTasks($taskList, $sortStrategy);
                }
            }
            
            $tasks = $tasksByStatus;

            include __DIR__ . '/../views/project/show.php';
        } else {
            echo "Project not found";
        }
    }


    private function getTasksByStatus(int $projectId, string $status)
    {
        $taskRepository = new TaskRepository();
        $tasks = $taskRepository->getTasksByProjectIdAndStatus($projectId, $status);
        return $tasks;
    }

 public function inviteMember(int $projectId)
{
    $userId = $_SESSION['user_id'] ?? null;
    $projectRepository = new ProjectRepository();
    $projectMembers = $projectRepository->getProjectMembers($projectId);
    $isAdmin = false;
    
    foreach ($projectMembers as $member) {
        if ($member->user_id == $userId) {
            $userRole = $projectRepository->getUserRoleInProject($projectId, $userId);
            if ($userRole && $userRole->name === 'admin') {
                $isAdmin = true;
            }
            break;
        }
    }

    if (!$isAdmin) {
        echo "You do not have permission to invite members to this project.";
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $role = $_POST['role'];

        $inviteResult = $this->projectService->inviteMember($projectId, $email, $role);

        if ($inviteResult["success"]) {
            echo '<div class="alert alert-success">Invitation sent to ' . htmlspecialchars($email) . '</div>';
        } else {
            echo '<div class="alert alert-danger">' . htmlspecialchars($inviteResult['error']) . '</div>';
        }

        $roles = $this->projectService->getAllRoles();
        include __DIR__ . '/../views/project/invite_member.php';
    } else {
        $roles = $this->projectService->getAllRoles();
        include __DIR__ . '/../views/project/invite_member.php';
    }
}
    public function edit(int $id)
    {
        $userId = $_SESSION['user_id'] ?? null;
        $projectRepository = new ProjectRepository();
        $projectMembers = $projectRepository->getProjectMembers($id);
        $isAdmin = false;
        
        foreach ($projectMembers as $member) {
            if ($member->user_id == $userId) {
                $userRole = $projectRepository->getUserRoleInProject($id, $userId);
                if ($userRole && $userRole->name === 'admin') {
                    $isAdmin = true;
                }
                break;
            }
        }

        if (!$isAdmin) {
            echo "You do not have permission to edit this project.";
            return;
        }

        $project = $this->projectService->getProjectById($id);

        if (!$project) {
            echo "Project not found";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $type = $_POST['type'];

            $command = new UpdateProjectCommand(
                $this->projectService,
                $id,
                $name,
                $description,
                $start_date,
                $end_date,
                $type
            );
            
            $updateResult = $command->execute();

            if ($updateResult["success"]) {
                $project = $this->projectService->getProjectById($id);
                
                $projectNotifier = new ProjectNotifier();
                $projectNotifier->attach(new LogObserver());
                $projectNotifier->attach(new UserNotificationObserver());
                
                $projectNotifier->notify(
                    NotificationType::PROJECT_UPDATE,
                    [
                        'project' => $project,
                        'actor_user_id' => $_SESSION['user_id'],
                        'actor_name' => $_SESSION['username'],
                        'entity_type' => 'project',
                        'entity_id' => $id
                    ]
                );

                header("Location: /projet_java/app/Projects");
                exit;
            } else {
                $error = $updateResult['error'];
                include __DIR__ . '/../views/project/edit.php';
            }
        } else {
            include __DIR__ . '/../views/project/edit.php';
        }
    }


public function delete(int $id)
{
    $userId = $_SESSION['user_id'] ?? null;
    $projectRepository = new ProjectRepository();
    $projectMembers = $projectRepository->getProjectMembers($id);
    $isAdmin = false;
    
    foreach ($projectMembers as $member) {
        if ($member->user_id == $userId) {
            $userRole = $projectRepository->getUserRoleInProject($id, $userId);
            if ($userRole && $userRole->name === 'admin') {
                $isAdmin = true;
            }
            break;
        }
    }

    if (!$isAdmin) {
        echo "You do not have permission to delete this project.";
        return;
    }

    $project = $this->projectService->getProjectById($id);
    
    if (!$project) {
        echo "Project not found";
        return;
    }

    $projectNotifier = new ProjectNotifier();
    $projectNotifier->attach(new LogObserver());
    $projectNotifier->attach(new UserNotificationObserver());
    
    $projectNotifier->notify(
        NotificationType::PROJECT_DELETED,
        [
            'project' => $project, 
            'actor_user_id' => $_SESSION['user_id'],
            'actor_name' => $_SESSION['username'],
            'entity_type' => 'project', 
            'entity_id' => $id
        ]
    );

    $command = new DeleteProjectCommand($this->projectService, $id);
    $deleteResult = $command->execute();

    if ($deleteResult["success"]) {
        header("Location: /projet_java/app/Projects");
        exit;
    } else {
        echo "Error deleting project: " . ($deleteResult['error'] ?? 'Unknown error');
    }
}
    
}
?>
