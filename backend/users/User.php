<?php

require_once '../BaseModel.php';

class User extends BaseModel {

    public function createUser($username, $password, $email, $firstName, $lastName, $profile, $role) {
        try {
            $query = "SELECT COUNT(*) FROM users WHERE email = :email";
            $params = [':email' => $email];
            $stmt = $this->executeQuery($query, $params);
            if (is_string($stmt)) {
                return $stmt;
            }
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                return "User already exists.";
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $query = "INSERT INTO users (username, password, email, first_name, last_name, profile, role) VALUES (:username, :password, :email, :first_name, :last_name, :profile, :role)";
            $params = [
                ':username' => $username,
                ':password' => $hashedPassword,
                ':email' => $email,
                ':first_name' => $firstName,
                ':last_name' => $lastName,
                ':profile' => $profile,
                ':role' => $role
            ];
            $stmt = $this->executeQuery($query, $params);
            if (is_string($stmt)) {
                return $stmt;
            }
            return true;
        } catch (Exception $e) {
            error_log("Error in User.php: " . $e->getMessage());
            return $e->getMessage();
        }
    }

    public function deleteUser($id) {
        try {
            $query = "DELETE FROM users WHERE id = :id";
            $params = [':id' => $id];
            $stmt = $this->executeQuery($query, $params);
            if (is_string($stmt)) {
                return $stmt;
            }
            return true;
        } catch (Exception $e) {
            error_log("Error in User.php: " . $e->getMessage());
            return $e->getMessage();
        }
    }

    public function getUser($id) {
        try {
            $query = "SELECT * FROM users WHERE id = :id";
            $params = [':id' => $id];
            $stmt = $this->executeQuery($query, $params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC); // fetchAllからfetchに変更
            return $result ? $result : [];
        } catch (Exception $e) {
            error_log("Error in User.php: " . $e->getMessage());
            return null;
        }
    }

    public function login($email, $password) {
        try {
            $query = "SELECT id, password FROM users WHERE email = :email";
            $params = [':email' => $email];
            $stmt = $this->executeQuery($query, $params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($result && password_verify($password, $result['password'])) {
                $id = $result['id'];
                $hashedPassword = $result['password'];
                return ['id' => $id, 'password' => $hashedPassword];
            } else {
                return null;
            }
        } catch (Exception $e) {
            error_log("Error in User.php: " . $e->getMessage());
            return null;
        }
    }
}