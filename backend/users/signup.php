<?php


require '../connect.php';
require 'User.php';
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $username = $input['username'] ?? null;
    $password = $input['password'] ?? null;
    $email = $input['email'] ?? null;
    $firstName = $input['first_name'] ?? null;
    $lastName = $input['last_name'] ?? null;
    $profile = $input['profile'] ?? null;
    $role = $input['role'] ?? null;

    if ($username && $password && $email && $firstName && $lastName && $role) {
        $db = new Database();
        $connection = $db->getConnection();
        $user = new User($connection);
        $result = $user->createUser($username, $password, $email, $firstName, $lastName, $profile, $role);

        if ($result === true) {
            echo json_encode(['message' => 'User created successfully.']);
        } elseif ($result === "User already exists.") {
            http_response_code(400);
            echo json_encode(['message' => 'User already exists.']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to create user.', 'error' => $result]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'All fields are required.']);
    }
} catch (Exception $e) {
    error_log("Error in signup.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Internal server error.', 'error' => $e->getMessage()]);
}
