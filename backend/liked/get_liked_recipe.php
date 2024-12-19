<?php
require '../display_error.php';
require '../cors.php';
require 'Like.php';
require '../auth.php';
require_once '../auditrecord.php';

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);

    $db = new Database();
    $connection = $db->getConnection();

    $auth = new Auth($connection);
    $validUserId = $auth->checkAuth($input);

    $like = new Like($connection);
    $Result = $like->getLikedRecipes($validUserId);

    echo json_encode($Result);

    } catch (Exception $e) {
        $audit = new Audit($connection);
        $audit->record(null, 'GET', $e->getMessage(), $_SERVER['REMOTE_ADDR']);

        http_response_code(500);
        echo json_encode(['message' => 'Internal server error.', 'error' => $e->getMessage()]);
}
