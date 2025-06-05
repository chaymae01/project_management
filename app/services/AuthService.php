<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Config\Database;
use PDO;

class AuthService
{
  private $userRepository;

  public function __construct(UserRepository $userRepository)
  {
    $this->userRepository = $userRepository;
  }

  public function login(string $email, string $password)
  {
    $user = $this->userRepository->findByEmail($email);

    if ($user && password_verify($password, $user->password)) {
      return $user;
    }

    return null;
  }

  public function register(string $username, string $email, string $password)
  {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    return $this->userRepository->create($username, $email, $hashedPassword);
  }

  public function registerUserWithInvite(string $username, string $email, string $password, string $token): array
  {
    $db = Database::getInstance()->getConnection();

    $stmt = $db->prepare("SELECT * FROM project_invitations WHERE email = :email AND token = :token");
    $stmt->execute(['email' => $email, 'token' => $token]);
    $invitation = $stmt->fetch(PDO::FETCH_OBJ);

    if ($invitation) {
      $user = $this->register($username, $email, $password);

      if ($user) {
        $userId = $user->id;
        $projectId = $invitation->project_id;
        $roleId = $invitation->role_id;

        $stmt = $db->prepare("INSERT INTO project_members (project_id, user_id, role_id) VALUES (:project_id, :user_id, :role_id)");
        $stmt->execute([
          'project_id' => $projectId,
          'user_id' => $userId,
          'role_id' => $roleId
        ]);

        $stmt = $db->prepare("DELETE FROM project_invitations WHERE email = :email AND token = :token");
        $stmt->execute(['email' => $email, 'token' => $token]);

        return ['success' => true];
      } else {
        return ['success' => false, 'error' => "Registration failed."];
      }
    } else {
      return ['success' => false, 'error' => "Invalid invitation token."];
    }
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
}
?>
