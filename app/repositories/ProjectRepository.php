<?php
namespace App\Repositories;

use App\Config\Database;
use App\Models\Entities\Project;
use PDO;
use PDOException;

class ProjectRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create(Project $project, int $creator_id)
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("INSERT INTO projects (name, description, start_date, end_date, creator_id, type, board) 
                                      VALUES (:name, :description, :start_date, :end_date, :creator_id, :type, :board)");
            $stmt->execute([
                'name' => $project->name,
                'description' => $project->description,
                'start_date' => $project->start_date,
                'end_date' => $project->end_date,
                'creator_id' => $creator_id,
                'type' => $project->type,
                'board' => json_encode($project->board)
            ]);

            $projectId = $this->db->lastInsertId();

            $stmt = $this->db->prepare("SELECT id FROM roles WHERE name = 'admin'");
            $stmt->execute();
            $role = $stmt->fetch(PDO::FETCH_OBJ);

            if ($role) {
                $adminRoleId = $role->id;

                $stmt = $this->db->prepare("INSERT INTO project_members (project_id, user_id, role_id) 
                                          VALUES (:project_id, :user_id, :role_id)");
                $stmt->execute([
                    'project_id' => $projectId,
                    'user_id' => $creator_id,
                    'role_id' => $adminRoleId
                ]);
            } else {
                throw new \Exception("Admin role not found.");
            }

            $this->db->commit();
            $project->id = $projectId; 
            return $project;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

     public function findById(int $id)
{
    try {
        $stmt = $this->db->prepare("SELECT * FROM projects WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $project = $stmt->fetch(PDO::FETCH_OBJ);

        if ($project) {
            $project->board = isset($project->board) 
                ? json_decode($project->board, true) 
                : [];
                
            error_log("Board content: " . print_r($project->board, true));
        }

        return $project;
    } catch (PDOException $e) {
        throw new \Exception("Database error: " . $e->getMessage());
    }
}

      public function getProjectsByUserId(int $userId)
      {
        try {
          $stmt = $this->db->prepare("
            SELECT 
              p.id,
              p.name,
              p.description,
              p.start_date,
              p.end_date,
              pm.role_id,
              r.name AS role_name
            FROM projects p
            INNER JOIN project_members pm ON p.id = pm.project_id
            INNER JOIN roles r ON pm.role_id = r.id
            WHERE pm.user_id = :user_id
          ");
          $stmt->execute(['user_id' => $userId]);
          $stmt->setFetchMode(PDO::FETCH_OBJ);
          return $stmt->fetchAll();
        } catch (PDOException $e) {
          throw new \Exception("Database error: " . $e->getMessage());
        }
      }

      public function getProjectMembers(int $projectId)
      {
        $stmt = $this->db->prepare("
            SELECT 
                pm.user_id,
                u.username
            FROM project_members pm
            JOIN users u ON pm.user_id = u.id
            WHERE pm.project_id = :project_id
        ");
        $stmt->execute(['project_id' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
      }

      public function getUserRoleInProject(int $projectId, int $userId)
      {
        $stmt = $this->db->prepare("
          SELECT r.name 
          FROM project_members pm 
          JOIN roles r ON pm.role_id = r.id 
          WHERE pm.project_id = :project_id AND pm.user_id = :user_id
        ");
        $stmt->execute(['project_id' => $projectId, 'user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_OBJ);
      }

      public function update(int $id, string $name, string $description, string $start_date, string $end_date, string $type)
      {
        $stmt = $this->db->prepare("UPDATE projects SET name = :name, description = :description, start_date = :start_date, end_date = :end_date, type = :type WHERE id = :id");
        $stmt->execute([
          'id' => $id,
          'name' => $name,
          'description' => $description,
          'start_date' => $start_date,
          'end_date' => $end_date,
          'type' => $type
        ]);
      }

      public function delete(int $id)
      {
        $stmt = $this->db->prepare("DELETE FROM projects WHERE id = :id");
        $stmt->execute(['id' => $id]);
      }
    
    public function isUserMember(int $projectId, int $userId): bool
{
    $stmt = $this->db->prepare("SELECT COUNT(*) FROM project_members 
                               WHERE project_id = :project_id 
                               AND user_id = :user_id");
    $stmt->execute([
        'project_id' => $projectId,
        'user_id' => $userId
    ]);
    return $stmt->fetchColumn() > 0;
}

public function isUserAdmin(int $projectId, int $userId): bool
{
    $stmt = $this->db->prepare("SELECT COUNT(*) FROM project_members pm
                               JOIN roles r ON pm.role_id = r.id
                               WHERE pm.project_id = :project_id 
                               AND pm.user_id = :user_id
                               AND r.name = 'admin'");
    $stmt->execute([
        'project_id' => $projectId,
        'user_id' => $userId
    ]);
    return $stmt->fetchColumn() > 0;
}


public function getProjectsByUserIdash(int $userId): array
{
    $stmt = $this->db->prepare("
        SELECT p.* 
        FROM projects p
        JOIN project_members pm ON p.id = pm.project_id
        WHERE pm.user_id = :user_id
        ORDER BY p.id DESC
    ");
    $stmt->execute(['user_id' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}
}

    
    ?>
