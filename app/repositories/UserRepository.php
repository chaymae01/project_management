<?php

    namespace App\Repositories;

    use App\Config\Database;
    use PDO;

    class UserRepository
    {
      private $db;

      public function __construct()
      {
        $this->db = Database::getInstance()->getConnection();
      }

      public function findByEmail(string $email)
      {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        return $stmt->fetch();
      }

      public function create(string $username, string $email, string $password)
      {
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $stmt->execute(['username' => $username, 'email' => $email, 'password' => $password]);

        $userId = $this->db->lastInsertId();
        return $this->findById($userId);
      }

      public function findById(int $id)
      {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        return $stmt->fetch();
      }
    }
