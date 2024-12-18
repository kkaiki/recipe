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
    
            // Get the last inserted ID
            $userId = $this->db->lastInsertId();
    
            return ['id' => $userId, 'hashed_password' => $hashedPassword];
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

    public function updateUser($id, $username, $email, $firstName, $lastName, $profile) {
        try {
            $fields = [];
            $params = [':id' => $id];
            $columns = ['username' => $username, 'email' => $email, 'first_name' => $firstName, 'last_name' => $lastName, 'profile' => $profile];

            foreach ($columns as $column => $value) {
                if ($value !== null) {
                    $fields[] = "$column = :$column";
                    $params[":$column"] = $value;
                }
            }

            if (empty($fields)) {
                throw new Exception("No fields to update.");
            }

            $query = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
            $stmt = $this->executeQuery($query, $params);
            return true;
        } catch (Exception $e) {
            error_log("Error in User.php: " . $e->getMessage());
            return $e->getMessage();
        }
    }

    public function updateUserRole($id, $change_role, $user_role) {
        try {
            $fields = [];
            $params = [':id' => $id];
            $columns = ['role' => $change_role];

            if ($user_role == 'editer' && $change_role == 'admin') {
                throw new Exception('You do not have permission to update user roles.');
            }

            foreach ($columns as $column => $value) {
                $fields[] = "$column = :$column";
                if ($value !== null) {
                    $params[":$column"] = $value;
                }
            }

            if (empty($fields)) {
                throw new Exception("No fields to update.");
            }

            $query = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
            $stmt = $this->executeQuery($query, $params);
            return true;
        } catch (Exception $e) {
            error_log("Error in User.php: " . $e->getMessage());
            return $e->getMessage();
        }
    }

    public function getRoleByUserId($userId) {
        try {
            $query = "SELECT role FROM users WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($result) {
                return $result['role'];
            } else {
                error_log("No user found with ID: $userId");
                return false;
            }
        } catch (Exception $e) {
            error_log("Error in getRoleByUserId: " . $e->getMessage());
            return false;
        }
    }

    public function getAllUsers() {
        try {
            $query = "SELECT * FROM users";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error in getAllUsers: " . $e->getMessage());
            return null;
        }
    }
}
