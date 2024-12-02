<?php
class Database {
    private $connection;
    private $host = 'localhost';
    private $db = 'recipe_db';
    private $user = 'root';
    private $pass = 'mysql';

    public function __construct() {
        try {
            $this->connection = new PDO("mysql:host={$this->host};dbname={$this->db}", $this->user, $this->pass);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->connection;
    }
}
