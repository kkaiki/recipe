<!-- 
parameters:
- username
- first_name
- lastName
- profile
- role

response:
nothing(only 200 http status code)
-->
<?php
require "../display_error.php";
require_once 'User.php';
require_once '../auth.php';
require_once '../auditrecord.php';
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);

    $db = new Database();
    $connection = $db->getConnection();

    $auth = new Auth($connection);
    $validUserId = $auth->checkAuth($input);

    $auth = new Auth(db: $connection);
    $validUserId = $auth->checkAuth($input);

    $username = $input['username'] ?? null;
    $firstName = $input['first_name'] ?? null;
    $lastName = $input['last_name'] ?? null;
    $profile = $input['profile'] ?? null;
    $email = $input['email'] ?? null;

    $user = new User($connection);
    $result = $user->updateUser($validUserId, $username, $email, $firstName, $lastName, $profile);

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
    echo json_encode(['message' => 'Internal server error.', 'error' => $e->getMessage()]);
}
