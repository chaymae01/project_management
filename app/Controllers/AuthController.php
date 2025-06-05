<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\ProjectService;
use App\Services\TaskService;
use App\Repositories\TaskRepository;

class AuthController
{
  private $authService;
  private $projectService;

  public function __construct(AuthService $authService, ProjectService $projectService)
  {
    $this->authService = $authService;
    $this->projectService = $projectService;
  }

  public function login()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $email = $_POST['email'];
      $password = $_POST['password'];
      $invite_token = $_POST['invite_token'] ?? null;

      $user = $this->authService->login($email, $password);

      if ($user) {
       
      if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;

        if ($invite_token) {
          $emailFromToken = $this->projectService->getEmailFromInviteToken($invite_token);
          if ($emailFromToken === $email) {
            // Add user to project members
            $addResult = $this->projectService->addUserToProjectFromInvite($user->id, $invite_token);
            if (!$addResult['success']) {
              echo "Error adding user to project: " . htmlspecialchars($addResult['error']);
            }
          }
        }

        header('Location: /projet_java/app/Dashboard');
        exit;
      } else {
        $error = "Invalid credentials.";
        include __DIR__ . '/../views/auth/login.php'; 
      }
    } else {
      include __DIR__ . '/../views/auth/login.php';
    }
  }

  public function register()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $username = $_POST['username'];
      $email = $_POST['email'];
      $password = $_POST['password'];
      $token = $_POST['token']; 

      $registrationResult = $this->authService->registerUserWithInvite($username, $email, $password, $token);

      if ($registrationResult['success']) {
        header('Location: /projet_java/app/login');
        exit;
      } else {
       
        $error = $registrationResult['error'];
        include __DIR__ . '/../views/auth/register.php'; 
      }
    } else {
      include __DIR__ . '/../views/auth/register.php';
    }
  }

 public function logout()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    session_destroy();
    header('Location: /projet_java/app/login');
    exit;
}


  public function acceptInvite()
  {
    $token = $_GET['token'];

    $email = $this->projectService->getEmailFromInviteToken($token);

    if ($email) {
      $userExists = $this->authService->checkIfUserExists($email);

      if ($userExists) {
      
        header('Location: /projet_java/app/login?invite_token=' . htmlspecialchars($token) . '&email=' . htmlspecialchars($email) . '&message=You already have an account. Please login to join the project.');
        exit;
      } else {
      
        header("Location: /projet_java/app/register?token=" . htmlspecialchars($token) . "&email=" . htmlspecialchars($email));
        exit;
      }
    } else {
     
      echo "Invalid invitation token.";
    }
  }
}
?>
