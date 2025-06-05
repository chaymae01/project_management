<?php
namespace App\Controllers;

use App\Models\Entities\Task;
use App\Patterns\Command\CreateTaskCommand;
use App\Patterns\Command\UpdateTaskCommand;
use App\Patterns\Command\DeleteTaskCommand;
use App\Patterns\Command\CommandInvoker;
use App\Patterns\Observer\LogObserver;
use App\Patterns\Observer\TaskNotifier;
use App\Patterns\Observer\UserNotificationObserver; 
use App\Patterns\Command\MoveTaskCommand;
use App\Patterns\State\TaskStateContext;
use App\Patterns\State\TodoState;
use App\Patterns\State\InProgressState;
use App\Patterns\State\DoneState;
use App\Services\TaskService;
use App\Repositories\{ProjectRepository, TaskRepository,UserRepository};
use App\Patterns\Factory\TaskFactoryProvider;
use App\Patterns\Composite\{SimpleTask, ComplexTask};
use App\Services\TaskCompositeService;
use App\Models\Entities\NotificationType; 

class TaskController
{
    private $taskService;
    private $compositeService;
    private $taskRepository;
    private $taskFactory;
    private $commandInvoker;

 private $projectRepository;

public function __construct(
    TaskService $taskService, 
    TaskCompositeService $compositeService,
    TaskRepository $taskRepository,
    TaskFactoryProvider $taskFactory,
    CommandInvoker $commandInvoker,
    ProjectRepository $projectRepository
) {
    $this->taskService = $taskService;
    $this->compositeService = $compositeService;
    $this->taskRepository = $taskRepository;
    $this->taskFactory = $taskFactory;
    $this->commandInvoker = $commandInvoker;
    $this->projectRepository = $projectRepository; 
}

 public function create()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $project_id = $_POST['project_id'];
        $creator_id = $_SESSION['user_id'];
        $assignee_id = $_POST['assignee_id'] ?? null;
        $taskType = $_POST['task_type'] ?? 'standard';
        $parent_id = $_POST['parent_id'] ?? null;
        $deadline = $_POST['deadline'] ?? null;

        $task = $this->taskFactory->getFactory($taskType)->createTask(
            $title,
            $description,
            $project_id,
            $creator_id,
            $assignee_id,
            $deadline
        );

        if ($parent_id !== null) {
            $task->setParentId($parent_id);
        }

        $createTaskCommand = new CreateTaskCommand($this->taskRepository, $task);
        $this->commandInvoker->executeCommand($createTaskCommand);

        // Notification
        $taskNotifier = new TaskNotifier();
        $taskNotifier->attach(new LogObserver());
        $taskNotifier->attach(new UserNotificationObserver());
        
        $taskNotifier->notify(
            NotificationType::TASK_CREATED,
            [
                'task' => $task,
                'assignee_id' => $assignee_id,
                'old_assignee_id' => null,
                'actor_user_id' => $creator_id,
                'actor_name' => $_SESSION['username'],
                'entity_type' => 'task',
                'entity_id' => $task->id
            ]
        );

        header('Location: /projet_java/app/project/show/' . $task->project_id);
        exit;
    } else {
        $project_id = $_GET['project_id'] ?? null;
        $parent_id = $_GET['parent_id'] ?? null;
        
        if ($project_id === null) {
            echo "Project ID is required";
            exit;
        }

        $projectRepository = new ProjectRepository();
        $projectMembers = $projectRepository->getProjectMembers($project_id);
        include __DIR__ . '/../views/task/create.php';
    }
}

    public function update(int $id)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $task = $this->taskService->find($id);

        if (!$task) {
            echo "Task not found";
            exit;
        }

        $task->setTitle($_POST['title']);
        $task->setDescription($_POST['description']);
        $task->setTaskType($_POST['task_type']);
        $task->setDeadline($_POST['deadline'] ?? null);
        $task->setAssigneeId($_POST['assignee_id'] ?? null);

      
        $newStatus = $_POST['status'];
        $newState = match ($newStatus) {
            'To Do' => new TodoState(),
            'In Progress' => new InProgressState(),
            'Done' => new DoneState(),
            default => throw new \Exception("Invalid status"),
        };

   
        $updateTaskCommand = new UpdateTaskCommand(
            $this->taskRepository, 
            $task, 
            $newState, 
            $this->compositeService
        );
        $this->commandInvoker->executeCommand($updateTaskCommand);

        $taskNotifier = new TaskNotifier();
        $taskNotifier->attach(new LogObserver());
        $taskNotifier->attach(new UserNotificationObserver());
        
       $taskNotifier->notify(
    NotificationType::TASK_UPDATED,
    [
        'task' => $task,
        'assignee_id' => $_POST['assignee_id'] ?? null,
        'old_assignee_id' => $task->getAssigneeId(),
        'actor_user_id' => $_SESSION['user_id'],
        'actor_name' => $_SESSION['username'],
        'entity_type' => 'task',
        'entity_id' => $task->id
    ]
);

        header('Location: /projet_java/app/project/show/' . $task->project_id);
        exit;
    } else {
        $task = $this->taskService->find($id);
        $projectMembers = $this->projectRepository->getProjectMembers($task->project_id);
        include __DIR__ . '/../views/task/update.php';
    }
}

    public function delete(int $id)
    {
      $task = $this->taskService->find($id);

      if (!$task) {
        echo "Task not found";
        exit;
      }

      $deleteTaskCommand = new DeleteTaskCommand($this->taskRepository, $task, $this->compositeService);
      $this->commandInvoker->executeCommand($deleteTaskCommand);

      $taskTitle = $task->title;
      $taskId = $task->id;
      $projectId = $task->project_id;

      $taskNotifier = new TaskNotifier();
      $taskNotifier->attach(new LogObserver());
      $taskNotifier->attach(new UserNotificationObserver());
      
      $taskNotifier->notify(
          NotificationType::TASK_DELETED,
          [
              'task' => $task,
              'actor_user_id' => $_SESSION['user_id'],
              'actor_name' => $_SESSION['username'],
              'entity_type' => 'task',
              'entity_id' => $taskId
          ]
      );

    header('Location: /projet_java/app/project/show/' . $task->project_id);   exit;
    }

  public function view(int $id)
{
    try {
        $task = $this->taskService->find($id);
        
        if (!$task) {
            throw new \Exception("Tâche non trouvée");
        }

        $projectRepository = new ProjectRepository();
        if (!$projectRepository->isUserMember($task->project_id, $_SESSION['user_id'])) {
            throw new \Exception("Vous n'avez pas accès à cette tâche");
        }

        $project = $projectRepository->findById($task->project_id);
        $userRepository = new UserRepository();
        $creator = $userRepository->findById($task->creator_id);
        $assignee = $task->assignee_id ? $userRepository->findById($task->assignee_id) : null;
        $parentTask = $task->parent_id ? $this->taskService->find($task->parent_id) : null;
        
        $subtasks = [];
        if ($task->task_type === 'complex') {
            $subtasks = $this->taskRepository->getTasksByProjectIdFiltered(
                $task->project_id, 
                null,  
                $task->id  
            );
        }

        include __DIR__ . '/../views/task/view.php';
        
    } catch (\Exception $e) {
        error_log($e->getMessage());
        http_response_code(404);
        echo $e->getMessage();
        exit;
    }
}

    public function addSubtask(int $parentId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $description = $_POST['description'];
            $creator_id = $_SESSION['user_id'];
            
            $parentTask = $this->taskService->find($parentId);
            
            $task = $this->taskFactory->getFactory('standard')->createTask(
                $title,
                $description,
                $parentTask->project_id,
                $creator_id
            );
            
            $this->taskRepository->addSubtask($parentId, $task);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }
        
        include __DIR__ . '/../views/task/add_subtask.php';
    }

    public function updateStatus(int $taskId)
{
    header('Content-Type: application/json');
    
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $newStatus = $data['status'] ?? null;
        
        $task = $this->taskService->find($taskId);
        if (!$task) throw new \Exception("Task not found");

        $context = new TaskStateContext($task);

        switch ($newStatus) {
            case 'To Do':
                $context->transitionToTodo();
                break;
            case 'In Progress':
                $context->transitionToInProgress();
                break;
            case 'Done':
                $context->transitionToDone();
                break;
            default:
                throw new \Exception("Invalid status");
        }

        $moveCommand = new MoveTaskCommand(
            $this->taskRepository,
            $task,
            $newStatus
        );
        $this->commandInvoker->executeCommand($moveCommand);

        $taskNotifier = new TaskNotifier();
        $taskNotifier->attach(new LogObserver());
         $taskNotifier->notify(
                NotificationType::STATUS_CHANGE,
                [
                    'task' => $task,
                    'new_status' => $newStatus,
                    'actor_user_id' => $_SESSION['user_id'],
                    'actor_name' => $_SESSION['username'],
                    'entity_type' => 'task',
                    'entity_id' => $taskId
                ]
            );

        echo json_encode([
            'success' => true,
            'newStatus' => $newStatus,
            'task' => $task->toArray()
        ]);
        
    } catch (\Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    exit;
}

    public function move(int $taskId)
    {
        header('Content-Type: application/json');
        
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $newStatus = $data['status'] ?? null;
            
            $task = $this->taskService->find($taskId);
            if (!$task) throw new \Exception("Task not found");

            $moveCommand = new MoveTaskCommand(
                $this->taskRepository,
                $task,
                $newStatus
            );
            
            $this->commandInvoker->executeCommand($moveCommand);

            $taskNotifier = new TaskNotifier();
            $taskNotifier->attach(new LogObserver());
            $taskNotifier->attach(new UserNotificationObserver());
            
            $taskNotifier->notify(
                NotificationType::STATUS_CHANGE,
                [
                    'task' => $task,
                    'new_status' => $newStatus,
                    'actor_user_id' => $_SESSION['user_id'],
                    'actor_name' => $_SESSION['username'],
                    'entity_type' => 'task',
                    'entity_id' => $taskId
                ]
            );

            echo json_encode([
                'success' => true,
                'newStatus' => $newStatus
            ]);
            
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        } finally {
            exit;
        }
    }
}
