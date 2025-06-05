<?php
namespace App\Services;

use App\Models\Entities\Project;
use App\Repositories\ProjectRepository;
use App\Config\Database;
use PDO;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/../../vendor/autoload.php';

class ProjectService
{
    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function createProject(Project $project, int $creator_id): array
    {
        try {
            $createdProject = $this->projectRepository->create($project, $creator_id);
            return ['success' => true, 'project' => $createdProject];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

  public function getProjectById(int $id)
  {
    return $this->projectRepository->findById($id);
  }

  public function inviteMember(int $projectId, string $email, string $role): array
  {
    $token = bin2hex(random_bytes(32));

    $db = Database::getInstance()->getConnection();

    $stmt = $db->prepare("SELECT id FROM roles WHERE name = :role");
    $stmt->execute(['role' => $role]);
    $roleData = $stmt->fetch(PDO::FETCH_OBJ);

    if ($roleData) {
      $roleId = $roleData->id;

      try {
        $stmt = $db->prepare("INSERT INTO project_invitations (project_id, email, role_id, token) VALUES (:project_id, :email, :role_id, :token)");
        $stmt->execute([
          'project_id' => $projectId,
          'email' => $email,
          'role_id' => $roleId,
          'token' => $token
        ]);

        $mail = new PHPMailer(true);

        try {
          $mail->SMTPDebug = 0; 
        
          $mail->isSMTP();
          $mail->Host = 'smtp.gmail.com';
          $mail->Port = 587;
          $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
          $mail->SMTPAuth = true;
          $mail->Username = 'chaymae.elfahssi@etu.uae.ac.ma';
          $mail->Password = 'cneq wxii efdq dycr';

          
          $mail->setFrom('chaymae.elfahssi@etu.uae.ac.ma', 'Kanban App');

          $mail->addAddress($email);

          
          $mail->isHTML(true);
          $mail->Subject = 'Invitation to join project';
          $invitationLink = "http://localhost/projet_java/app/acceptInvite?token=" . $token;
          $mail->Body    = 'You have been invited to join project with ID: ' . $projectId . ' with role: ' . $role . '. Click <a href="' . $invitationLink . '">here</a> to accept the invitation.';
          $mail->AltBody = 'You have been invited to join project with ID: ' . $projectId . ' with role: ' . $role . '.  Copy this link to accept the invitation: ' . $invitationLink;

          error_log("About to call PHPMailer->send() for " . $email); 
          $mail->send();
          error_log("PHPMailer->send() called successfully for " . $email); 
          return ['success' => true];
        } catch (Exception $e) {
          error_log("PHPMailer->send() failed: " . $e->getMessage()); 
          error_log("PHPMailer error info: " . $mail->ErrorInfo); 
          return ['success' => false, 'error' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"];
        }
      } catch (Exception $e) {
        return ['success' => false, 'error' => "Database error: " . $e->getMessage()];
      }
    } else {
      return ['success' => false, 'error' => "Role not found."];
    }
  }

  public function getAllRoles()
  {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT id, name FROM roles");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }

  public function getEmailFromInviteToken(string $token)
  {
    $db = Database::getInstance()->getConnection();

    $stmt = $db->prepare("SELECT email FROM project_invitations WHERE token = :token");
    $stmt->execute(['token' => $token]);
    $invitation = $stmt->fetch(PDO::FETCH_OBJ);

    if ($invitation) {
      return $invitation->email;
    } else {
      return null;
    }
  }

  public function checkIfUserExists(string $email): bool
  {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_OBJ);

    return (bool) $user;
  }

  public function addUserToProjectFromInvite(int $userId, string $token): array
  {
    $db = Database::getInstance()->getConnection();

    $stmt = $db->prepare("SELECT * FROM project_invitations WHERE token = :token");
    $stmt->execute(['token' => $token]);
    $invitation = $stmt->fetch(PDO::FETCH_OBJ);

    if ($invitation) {
      $projectId = $invitation->project_id;
      $roleId = $invitation->role_id;

      $stmt = $db->prepare("INSERT INTO project_members (project_id, user_id, role_id) VALUES (:project_id, :user_id, :role_id)");
      $stmt->execute([
        'project_id' => $projectId,
        'user_id' => $userId,
        'role_id' => $roleId
      ]);

      $stmt = $db->prepare("DELETE FROM project_invitations WHERE token = :token");
      $stmt->execute(['token' => $token]);

      return ['success' => true];
    } else {
      return ['success' => false, 'error' => "Invalid invitation token."];
    }
  }

  public function updateProject(int $id, string $name, string $description, string $start_date, string $end_date, string $type): array
  {
    try {
      $this->projectRepository->update($id, $name, $description, $start_date, $end_date, $type);
      return ['success' => true];
    } catch (\Exception $e) {
      return ['success' => false, 'error' => $e->getMessage()];
    }
  }

  public function deleteProject(int $id): array
  {
    try {
      $this->projectRepository->delete($id);
      return ['success' => true];
    } catch (\Exception $e) {
      return ['success' => false, 'error' => $e->getMessage()];
    }
  }
}
?>
