<?php

namespace App\Repositories;

use App\Config\Database;
use App\Models\Entities\Task;
use PDO;
use PDOException;

class TaskRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }


    public function create(Task $task)
    {
      $stmt = $this->db->prepare("INSERT INTO tasks (title, description, project_id, creator_id, assignee_id, parent_id, status, priority, position, task_type, deadline) VALUES (:title, :description, :project_id, :creator_id, :assignee_id, :parent_id, :status, :priority, :position, :task_type, :deadline)");
$stmt->execute([
    'title' => $task->title,
    'description' => $task->description,
    'project_id' => $task->project_id,
    'creator_id' => $task->creator_id,
    'assignee_id' => $task->assignee_id,
    'parent_id' => $task->getParentId(),
    'status' => $task->status,
    'priority' => $task->priority,
    'position' => $task->position,
    'task_type' => $task->getTaskType(),
    'deadline' => $task->deadline
]);

        $taskId = $this->db->lastInsertId();
        return $this->findById($taskId);
    }

public function update(Task $task)
{
    $stmt = $this->db->prepare("UPDATE tasks SET 
        title = :title, 
        description = :description, 
        status = :status, 
        priority = :priority, 
        position = :position, 
        assignee_id = :assignee_id, 
        parent_id = :parent_id, 
        task_type = :task_type, 
        deadline = :deadline 
        WHERE id = :id");
    
    $stmt->execute([
        'title' => $task->title,
        'description' => $task->description,
        'status' => $task->getStatus(),
        'priority' => $task->priority,
        'position' => $task->position,
        'assignee_id' => $task->assignee_id,
        'parent_id' => $task->getParentId(),
        'task_type' => $task->getTaskType(),
        'deadline' => $task->deadline,
        'id' => $task->getId()
    ]);

    return $this->findById($task->getId());
}

    public function findById(int $id): ?Task
    {
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $taskData = $stmt->fetch(PDO::FETCH_OBJ);
        if ($taskData) {
            return $this->mapDataToTaskEntity($taskData);
        }
        return null;
    }

    public function getTasksByProjectIdFiltered(int $projectId, ?string $status = null, ?int $parentId = null): array
    {
        $sql = "SELECT * FROM tasks WHERE project_id = :project_id";
        $params = ['project_id' => $projectId];

        if ($status !== null) {
            $sql .= " AND status = :status";
            $params['status'] = $status;
        }

        if ($parentId !== null) {
            $sql .= " AND parent_id = :parent_id";
            $params['parent_id'] = $parentId;
        } else {
            
        }

        $sql .= " ORDER BY parent_id ASC, position ASC"; 

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $tasksData = $stmt->fetchAll(PDO::FETCH_OBJ);

        $taskEntities = [];
        foreach ($tasksData as $taskData) {
            $taskEntities[] = $this->mapDataToTaskEntity($taskData);
        }
        return $taskEntities;
    }

    public function countDirectChildren(int $taskId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM tasks WHERE parent_id = :parent_id");
        $stmt->execute(['parent_id' => $taskId]);
        return (int)$stmt->fetchColumn();
    }

 
public function findTaskWithDescendants(int $taskId): array {
    $stmt = $this->db->prepare("
        WITH RECURSIVE task_tree AS (
            SELECT * FROM tasks WHERE id = :task_id
            UNION ALL
            SELECT t.* FROM tasks t
            JOIN task_tree tt ON t.parent_id = tt.id
        ) SELECT * FROM task_tree ORDER BY parent_id, position
    ");
    $stmt->execute(['task_id' => $taskId]);
    return array_map([$this, 'mapDataToTaskEntity'], $stmt->fetchAll(PDO::FETCH_OBJ));
}

    private function fetchChildrenRecursive(int $parentId, array &$allTasks): void
    {
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE parent_id = :parent_id ORDER BY position ASC");
        $stmt->execute(['parent_id' => $parentId]);
        $childrenData = $stmt->fetchAll(PDO::FETCH_OBJ);

        foreach ($childrenData as $childData) {
            if (!isset($allTasks[$childData->id])) { 
                $childEntity = $this->mapDataToTaskEntity($childData);
                $allTasks[$childEntity->id] = $childEntity;
                $this->fetchChildrenRecursive($childEntity->id, $allTasks);
            }
        }
    }

  private function mapDataToTaskEntity($taskData): Task
{
    $taskEntity = new Task(
        $taskData->title,
        $taskData->description ?? '',
        $taskData->project_id,
        $taskData->creator_id
    );
    $taskEntity->id = $taskData->id;
    $taskEntity->status = $taskData->status;
    $taskEntity->priority = $taskData->priority;
    $taskEntity->position = $taskData->position;
    $taskEntity->assignee_id = $taskData->assignee_id;
    $taskEntity->parent_id = $taskData->parent_id;
    $taskEntity->task_type = $taskData->task_type;
    
    $taskEntity->deadline = $taskData->deadline ?? null;

    return $taskEntity;
}



    public function addSubtask(int $parentId, Task $subtask): Task
    {
        $subtask->parent_id = $parentId;
        return $this->create($subtask);
    }

    public function delete(int $id)
    {
        $stmt = $this->db->prepare("DELETE FROM tasks WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
    

    public function getTasksByProjectId(int $projectId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE project_id = :project_id ORDER BY parent_id ASC, position ASC");
        $stmt->execute(['project_id' => $projectId]);
        $tasksData = $stmt->fetchAll(PDO::FETCH_OBJ);

        $taskEntities = [];
        foreach ($tasksData as $taskData) {
            $taskEntities[] = $this->mapDataToTaskEntity($taskData);
        }

        return $taskEntities;
    }

    public function getTasksByProjectIdAndStatus(int $projectId, string $status): array
    {
        return $this->getTasksByProjectIdFiltered($projectId, $status, null);
    }

public function find(int $id): ?Task
{
    return $this->findById($id);
}



public function getRecentTasksByUser($userId, $limit = 5): array {
    $sql = "
        SELECT t.*, 
               p.name AS project_name, 
               u.username AS assignee_name
        FROM tasks t
        JOIN projects p ON t.project_id = p.id
        JOIN users u ON t.assignee_id = u.id
        WHERE t.assignee_id = :user_id
        ORDER BY t.id DESC
        LIMIT :limit
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

public function getOldAssigneeId(int $taskId): ?int {
    try {
        $stmt = $this->db->prepare(
            "SELECT assignee_id FROM task_history 
             WHERE task_id = ? 
             ORDER BY changed_at DESC 
             LIMIT 1"
        );
        $stmt->execute([$taskId]);
        return $stmt->fetchColumn() ?: null;
    } catch (PDOException $e) {
        error_log("Error getting old assignee: " . $e->getMessage());
        return null;
    }
}

}


?>
