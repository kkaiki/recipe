<?php

require 'Like.php';
require '../connect.php';
require_once '../auditrecord.php';

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $recipe_id = $input['recipe_id'] ?? null;

    if (!$recipe_id) {
        http_response_code(400);
        echo json_encode(['message' => 'Recipe ID is required.']);
        exit();
    }

    $db = new Database();
    $connection = $db->getConnection();

    $like = new Like($connection);
    $likeResult = $like->getLikesUserIds($recipe_id);

    echo json_encode($likeResult);

    } catch (Exception $e) {
        $audit = new Audit($connection);
        $audit->record(null, 'GET', $e->getMessage(), $_SERVER['REMOTE_ADDR']);

        http_response_code(500);
        echo json_encode(['message' => 'Internal server error.', 'error' => $e->getMessage()]);
}
