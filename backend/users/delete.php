<?php
require '../cors.php';
require_once 'User.php';
require_once '../auth.php';
require_once '../auditrecord.php';
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);

    $db = new Database();
    $connection = $db->getConnection();

    $auth = new Auth(db: $connection);
    $validUserId = $auth->checkAuth($input);

    $user = new User($connection);
    $result = $user->deleteUser($validUserId);

    if ($result === true) {
        echo json_encode(['message' => 'User deleted successfully.']);
    }

} catch (Exception $e) {
    $audit = new Audit($connection);
    $audit->record($input['local_storage_user_id'] ?? null, 'DELETE', $e->getMessage(), $_SERVER['REMOTE_ADDR']);
    http_response_code(500);
    echo json_encode(['message' => 'Internal server error.', 'error' => $e->getMessage()]);
}
