<?php
require '../cors.php';
require 'User.php';
require_once '../auth.php';
require_once '../auditrecord.php';

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $username = $input['local_storage_user_id'] ?? null;
    $password = $input['local_storage_user_password'] ?? null;

    if ($username) {
        $db = new Database();
        $connection = $db->getConnection();

        $auth = new Auth($connection);
        $validUserId = $auth->checkAuth($input);

        $user = new User($connection);
        $role = $user->getRoleByUserId($validUserId);

        if ($role) {
            echo json_encode(['role' => $role]);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'User not found.']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'Userid is required.']);
    }
} catch (Exception $e) {
    $audit = new Audit($connection);
    $audit->record(null, 'GET', $e->getMessage(), $_SERVER['REMOTE_ADDR']);

    error_log("Error in get_role.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Internal server error.', 'error' => $e->getMessage()]);
}
