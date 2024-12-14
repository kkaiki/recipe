<?php

require 'RecipeCategories.php';
require_once '../auth.php';
require_once '../auditrecord.php';

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $recipe_id = $input['recipe_id'] ?? null;
    $category_id = $input['category_id'] ?? null;

    $db = new Database();
    $connection = $db->getConnection();

    $auth = new Auth($connection);
    $validUserId = $auth->checkAuth($input);

    if (!$recipe_id || !$category_id) {
        http_response_code(400);
        echo json_encode(['message' => 'Recipe ID and Category ID are required.']);
        exit();
    }

    $recipeCategories = new RecipeCategories($connection);
    $result = $recipeCategories->addRecipeCategory($connection, $recipe_id, $category_id);

    echo json_encode(['message' => 'Recipe category added successfully', 'result' => $result]);

} catch (Exception $e) {
    $audit = new Audit($connection);
    $audit->record(null, 'POST', $e->getMessage(), $_SERVER['REMOTE_ADDR']);

    http_response_code(500);
    echo json_encode(['message' => 'Internal server error.', 'error' => $e->getMessage()]);
}