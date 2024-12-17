<?php
require '../cors.php';
require_once 'User.php';
require_once '../auth.php';
require_once '../auditrecord.php';
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);

    $change_role = $input['role'] ?? null;
    $target_user = $input['user_id'] ?? null;

    if (!$input['role'] || !$input['user_id']) {
        throw new Exception('missing role or user_id parameter.');
    }

    $db = new Database();
    $connection = $db->getConnection();

    $auth = new Auth($connection);
    $validUserId = $auth->checkAuth($input);

    $user = new User($connection);
    $user_role = $user->getRoleByUserId($validUserId);
    print($user_role);

    if ($user_role == 'viewer') {
        throw new Exception('You do not have permission to update user roles.');
    }

    $user = new User($connection);
    $result = $user->updateUserRole($target_user, $change_role, $user_role);

    if ($result === true) {
        http_response_code(200);
        echo json_encode(['message' => 'User updated successfully.']);
    } else {
        throw new Exception($result);
    }
} catch (Exception $e) {
    $audit = new Audit($connection);
    $audit->record($input['local_storage_user_id'] ?? null, 'UPDATE', $e->getMessage(), $_SERVER['REMOTE_ADDR']);

    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
