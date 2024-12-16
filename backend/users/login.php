<?php

require '../cors.php';
require_once 'User.php';
require_once '../auth.php';
require_once '../auditrecord.php';
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $email = $input['email'] ?? null;
    $password = $input['password'] ?? null;

    if (!$email || !$password)
        throw new Exception("email and password are required.");

    $db = new Database();
    $connection = $db->getConnection();

    $user = new User($connection);
    $userData = $user->login($email, $password);

    if (!$userData)
        throw new Exception("Invalid email or password.");

    echo json_encode(['id' => $userData['id'], 'password' => $userData['password']]);

} catch (Exception $e) {
    $audit = new Audit($connection);
    $audit->record(null, 'LOGIN', $e->getMessage(), $_SERVER['REMOTE_ADDR']);

    http_response_code(500);
    echo json_encode(['message' => 'Internal server error.', 'error' => $e->getMessage()]);
}
