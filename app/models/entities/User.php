<?php

    namespace App\Models\Entities;

    class User
    {
      public $id;
      public $username;
      public $email;
      public $password;
      public $created_at;

      public function __construct(int $id, string $username, string $email, string $password, string $created_at)
      {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->created_at = $created_at;
      }

      public function getId() { return $this->id; }
      public function setId($id) { $this->id = $id; }
      public function getUsername() { return $this->username; }
      public function setUsername($username) { $this->username = $username; }
      public function getEmail() { return $this->email; }
      public function setEmail($email) { $this->email = $email; }
      public function getPassword() { return $this->password; }
      public function setPassword($password) { $this->password = $password; }
      public function getCreatedAt() { return $this->created_at; }
      public function setCreatedAt($created_at) { $this->created_at = $created_at; }
    }
