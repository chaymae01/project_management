<?php
namespace App\Controllers;

use App\Services\NotificationService;
 use App\Repositories\TaskRepository;

class NotificationController
{
    private $notificationService;

    public function __construct()
    {
        $this->notificationService = new NotificationService();
    }

   
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /projet_java/app/login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $notifications = $this->notificationService->getUnreadNotifications($userId, 100); 

        include __DIR__ . '/../views/notifications/index.php';
    }

   public function view(int $id)
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: /projet_java/app/login');
        exit;
    }

    $userId = $_SESSION['user_id'];
    

    $this->notificationService->markAsRead($id, $userId);
    
    $notification = $this->notificationService->getNotificationById($id);
    
    if ($notification && $notification->related_entity_type && $notification->related_entity_id) {
        switch ($notification->related_entity_type) {
            case 'task':
                header('Location: /projet_java/app/project/show/' . $this->getProjectIdForTask($notification->related_entity_id));
                break;
            case 'project':
                header('Location: /projet_java/app/project/show/' . $notification->related_entity_id);
                break;
            default:
                header('Location: /projet_java/app/Projects');
        }
    } else {
        header('Location: /projet_java/app/Projects');
    }
    exit;
}

private function getProjectIdForTask(int $taskId)
{
    
    $taskRepository = new TaskRepository();
    $task = $taskRepository->find($taskId);
    return $task ? $task->project_id : null;
}

  
    public function markAllRead()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /projet_java/app/login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $this->notificationService->markAllAsRead($userId);
        
       
        $referer = $_SERVER['HTTP_REFERER'] ?? '/projet_java/app/Projects';
        header('Location: ' . $referer);
        exit;
    }
    public function count()
{
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['count' => 0]);
        exit;
    }

    $count = $this->notificationService->countUnreadNotifications($_SESSION['user_id']);
    echo json_encode(['count' => $count]);
    exit;
}

}
