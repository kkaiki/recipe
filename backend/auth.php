<?php

require 'connect.php';

class Auth {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function validateCredentials($userId, $hashedPassword) {
        try {
            $query = "SELECT id, password FROM users WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($result) {
                if ($hashedPassword === $result['password']) {
                    return $result['id'];
                } else {
                    error_log("Password verification failed for user ID: $userId");
                    return false;
                }
            } else {
                error_log("No user found with ID: $userId");
                return false;
            }
        } catch (Exception $e) {
            error_log("Error in validateCredentials: " . $e->getMessage());
            return false;
        }
    }

    public function checkAuth($input) {
        $userId = $input['local_storage_user_id'] ?? null;
        $password = $input['local_storage_user_password'] ?? null;

        if ($userId && $password) {
            $validUserId = $this->validateCredentials($userId, $password);

            if ($validUserId) {
                return $validUserId;
            } else {
                http_response_code(401);
                echo json_encode(['message' => 'Invalid credentials.']);
                exit;
            }
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'User ID and password are required.']);
            exit;
        }
    }
}
