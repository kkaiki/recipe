<?php

require 'RecipeCategories.php';
require_once '../auditrecord.php';

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $recipe_ids = $input['recipe_ids'] ?? null;

    $db = new Database();
    $connection = $db->getConnection();

    if (!$recipe_ids) {
        http_response_code(400);
        echo json_encode(['message' => 'Recipe IDs are required.']);
        exit();
    }

    $recipeCategories = new RecipeCategories($connection);
    $recipes = $recipeCategories->getRecipesByIds($connection, $recipe_ids);

    echo json_encode(['recipes' => $recipes]);

} catch (Exception $e) {
    $audit = new Audit($connection);
    $audit->record(null, 'GET', $e->getMessage(), $_SERVER['REMOTE_ADDR']);

    http_response_code(500);
    echo json_encode(['message' => 'Internal server error.', 'error' => $e->getMessage()]);
}
