<?php
namespace App\Patterns\Observer;

use App\Config\Database;
use App\Repositories\ProjectRepository;
use App\Services\NotificationService;
use App\Models\Entities\NotificationType;
use PDOException;

class UserNotificationObserver implements Observer
{
    private $db;
    private $projectRepository;
    private $notificationService;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->projectRepository = new ProjectRepository();
        $this->notificationService = new NotificationService();
    }

    public function update(string $eventType, $data)
    {
        $recipients = $this->determineRecipients($eventType, $data);
        if (empty($recipients)) {
            return;
        }

        $message = $this->generateMessage($eventType, $data);
        $entityType = $data['entity_type'] ?? 'unknown';
        $entityId = $data['entity_id'] ?? 0;

        foreach ($recipients as $userId) {
            if (isset($data['actor_user_id']) && $userId == $data['actor_user_id']) {
                continue;
            }
            $this->createNotification($userId, $message, $entityType, $entityId);
        }
    }

   
    private function determineRecipients(string $eventType, array $data): array
{
    $recipients = [];
    $actorId = $data['actor_user_id'] ?? null;

    if (isset($data['task'])) {
        $task = $data['task'];
        $projectId = $task->project_id;

        switch ($eventType) {
            case NotificationType::TASK_CREATED:
                if (!empty($task->assignee_id)) {
                    if ($task->assignee_id != $actorId) {
                        $recipients[] = $task->assignee_id;
                    }
                } else {
                    $projectMembers = $this->projectRepository->getProjectMembers($projectId);
                    foreach ($projectMembers as $member) {
                        if ($member->user_id != $actorId) {
                            $recipients[] = $member->user_id;
                        }
                    }
                }
                break;

            case NotificationType::TASK_UPDATED:
                if (isset($data['old_assignee_id'], $data['assignee_id'])) {
                    if ($data['old_assignee_id'] != $data['assignee_id']) {
                        if ($data['old_assignee_id'] && $data['old_assignee_id'] != $actorId) {
                            $recipients[] = $data['old_assignee_id'];
                        }
                        if ($data['assignee_id'] && $data['assignee_id'] != $actorId) {
                            $recipients[] = $data['assignee_id'];
                        }
                    }
                }
                if ($task->creator_id && $task->creator_id != $actorId) {
                    $recipients[] = $task->creator_id;
                }
                break;

            case NotificationType::TASK_DELETED:
                if ($task->assignee_id && $task->assignee_id != $actorId) {
                    $recipients[] = $task->assignee_id;
                }
                if ($task->creator_id && $task->creator_id != $actorId) {
                    $recipients[] = $task->creator_id;
                }
                break;

            case NotificationType::STATUS_CHANGE:
                if ($task->assignee_id && $task->assignee_id != $actorId) {
                    $recipients[] = $task->assignee_id;
                }
                if ($task->creator_id && $task->creator_id != $actorId) {
                    $recipients[] = $task->creator_id;
                }
                break;
        }
    } 
    elseif (isset($data['project'])) {
        $project = $data['project'];
        $members = $this->projectRepository->getProjectMembers($project->id);
        foreach ($members as $member) {
            if ($member->user_id != $actorId) {
                $recipients[] = $member->user_id;
            }
        }
    }

    return array_values(array_unique($recipients, SORT_NUMERIC));
}

private function generateMessage(string $eventType, array $data): string
{
    $actor = $data['actor_name'] ?? 'Système';
    $taskTitle = $data['task']->title ?? '';
    $projectName = $data['project']->name ?? '';

    switch ($eventType) {
        case NotificationType::TASK_CREATED:
            return isset($data['task']->assignee_id)
                ? "{$actor} vous a assigné la tâche '{$taskTitle}'"
                : "{$actor} a créé la tâche '{$taskTitle}' dans le projet";

        case NotificationType::TASK_UPDATED:
            if (isset($data['old_assignee_id'], $data['assignee_id']) && 
                $data['old_assignee_id'] != $data['assignee_id']) {
                return "{$actor} vous a assigné la tâche '{$taskTitle}'";
            }
            return "{$actor} a modifié la tâche '{$taskTitle}'";

        case NotificationType::TASK_DELETED:
            return "{$actor} a supprimé la tâche '{$taskTitle}'";

        case NotificationType::STATUS_CHANGE:
            return "Statut modifié : '{$taskTitle}' -> {$data['new_status']} par {$actor}";

        case NotificationType::PROJECT_UPDATE:
            return "{$actor} a modifié le projet '{$projectName}'";

        case NotificationType::PROJECT_DELETED:
            return "{$actor} a supprimé le projet '{$projectName}'";

        default:
            return "Nouvelle notification";
    }
}
private function getUserName(?int $userId): string {
    if (!$userId) return '';

    try {
        $stmt = $this->db->prepare("SELECT username FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn() ?: '';
    } catch (PDOException $e) {
        error_log("Error fetching username: " . $e->getMessage());
        return '';
    }
}


   
    private function createNotification(int $userId, string $message, string $entityType, int $entityId): bool
    {
        $sql = "INSERT INTO notifications 
               (user_id, message, related_entity_type, related_entity_id, is_read, created_at)
               VALUES (:user_id, :message, :entity_type, :entity_id, 0, NOW())";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':user_id' => $userId,
                ':message' => $message,
                ':entity_type' => $entityType,
                ':entity_id' => $entityId
            ]);
        } catch (PDOException $e) {
            error_log("[Notification DB Error] " . $e->getMessage());
            return false;
        }
    }
}
