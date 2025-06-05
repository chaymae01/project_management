<?php

    namespace App\Config;

    use PDO;
    use PDOException;

    class Database
    {
      private static $instance = null;
      private $connection;

      private $host = "localhost";
      private $db_name = "kanban";
      private $username = "root";
      private $password = "chaymae2002";
      private $port = '3308';

      private function __construct()
      {
        try {
          $this->connection = new PDO("mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name, $this->username, $this->password);
          $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
          echo "Connection error: " . $e->getMessage();
          exit;
        }
      }

      public static function getInstance()
      {
        if (!self::$instance) {
          self::$instance = new Database();
        }

        return self::$instance;
      }

      public function getConnection()
      {
        return $this->connection;
      }
    }
