<?php
namespace App\Repositories;

use App\Config\Database;
use PDO;

class NotificationRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getUnreadNotifications(int $userId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM notifications 
            WHERE user_id = :user_id AND is_read = 0
            ORDER BY created_at DESC
            LIMIT 5
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getRecentActivities(int $userId)
    {
        $stmt = $this->db->prepare("
            SELECT n.*, u.username as actor_name 
            FROM notifications n
            JOIN users u ON n.user_id = u.id
            WHERE n.user_id = :user_id OR n.related_entity_id IN (
                SELECT project_id FROM project_members WHERE user_id = :user_id
            )
            ORDER BY n.created_at DESC
            LIMIT 5
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}