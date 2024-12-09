<!-- 
to get user data from the database

expected parameters:
- username
- password

check:
- check authenication

return:
- id
- username
- first_name
- last_name
- email
- profile
- role
- liked recipes
- created recipes
-->
<?php

// require '../connect.php';
require 'User.php';
require_once '../auth.php';
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
        $userData = $user->getUser($validUserId);

        if ($userData) {
            echo json_encode($userData);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'User not found.']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'Username is required.']);
    }
} catch (Exception $e) {
    error_log("Error in getUser.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Internal server error.', 'error' => $e->getMessage()]);
}
