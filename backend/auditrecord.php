<?php
require_once 'BaseModel.php';

class Audit extends BaseModel {
    public function record($userId, $method, $errorMessage = null, $ipAddress = null) {
        try {
            $query = "INSERT INTO auditrecord (user_id, method, error_message, ip_address) VALUES (:user_id, :method, :error_message, :ip_address)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':method', $method, PDO::PARAM_STR);
            $stmt->bindParam(':error_message', $errorMessage, PDO::PARAM_STR);
            $stmt->bindParam(':ip_address', $ipAddress, PDO::PARAM_STR);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("Error in Audit.php: " . $e->getMessage());
            return false;
        }
    }
}