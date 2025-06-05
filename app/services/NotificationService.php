<?php
namespace App\Services;

use App\Config\Database;
use PDO;

class NotificationService {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

   
    public function getUnreadNotifications(int $userId, int $limit = 5): array {
        $sql = "SELECT * FROM notifications 
                WHERE user_id = :user_id AND is_read = 0 
                ORDER BY created_at DESC 
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

  
    public function countUnreadNotifications(int $userId): int {
        $sql = "SELECT COUNT(*) FROM notifications 
                WHERE user_id = :user_id AND is_read = 0";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return (int)$stmt->fetchColumn();
    }

  
    public function markAsRead(int $notificationId, int $userId): bool {
        $sql = "UPDATE notifications 
                SET is_read = 1 
                WHERE id = :id AND user_id = :user_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $notificationId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function markAllAsRead(int $userId): bool {
        $sql = "UPDATE notifications 
                SET is_read = 1 
                WHERE user_id = :user_id AND is_read = 0";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
public function createNotification(int $userId, string $message, string $entityType, ?int $entityId = null): bool
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
    } catch (\Exception $e) {
        error_log("[Notification Error] Failed to create notification: " . $e->getMessage());
        return false;
    }
}

public function getNotificationById(int $id)
{
    $sql = "SELECT * FROM notifications WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_OBJ);
}

}
