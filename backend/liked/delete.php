<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../connect.php';
require_once '../BaseModel.php';
require_once 'User.php';
require_once '../auth.php';
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $deleteId = $input['user_id'] ?? null;

    if ($deleteId === null) {
        http_response_code(400);
        echo json_encode(['message' => 'user_id is required.']);
        exit();
    }

    $db = new Database();
    $connection = $db->getConnection();
    $auth = new Auth($connection);
    $validUserId = $auth->checkAuth($input);

    if ($validUserId !== $deleteId) {
        http_response_code(400);
        echo json_encode(['message' => 'local_storage_user_id does not match user_id.']);
        exit();
    }

    $user = new User($connection);
    $result = $user->deleteUser($deleteId);

    if ($result === true) {
        echo json_encode(['message' => 'User deleted successfully.']);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to delete user.', 'error' => $result]);
    }
} catch (Exception $e) {
    error_log("Error in delete.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Internal server error.', 'error' => $e->getMessage()]);
}