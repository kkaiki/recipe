<?php

class BaseModel {
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    protected function executeQuery($query, $params) {
        try {
            $stmt = $this->db->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            if ($stmt->execute()) {
                return $stmt;
            } else {
                $errorInfo = $stmt->errorInfo();
                error_log("Failed to execute statement: " . implode(":", $errorInfo));
                return $errorInfo;
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return $e->getMessage();
        }
    }
}
