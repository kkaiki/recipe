<?php
require '../cors.php';
require_once '../auth.php';
require_once '../auditrecord.php';
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;

    if ($id) {
        $db = new Database();
        $connection = $db->getConnection();
        $auth = new Auth($connection);
        $auth->checkAuth($input);

        $query = "SELECT * FROM ingredient WHERE id = :id";
        $stmt = $connection->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $ingredient = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($ingredient) {
            echo json_encode($ingredient);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Ingredient not found.']);
        }
    } else {
        $audit = new Audit($connection);
        $audit->record($input['local_storage_user_id'], 'GET', "Error in get.php", $_SERVER['REMOTE_ADDR']);
        http_response_code(400);
        echo json_encode(['message' => 'ID is required.']);
    }
} catch (Exception $e) {
    $audit = new Audit($connection);
    $audit->record($input['local_storage_user_id'] ?? null, 'GET', $e->getMessage(), $_SERVER['REMOTE_ADDR']);
    http_response_code(500);
    echo json_encode(['message' => 'Error retrieving ingredient.', 'error' => $e->getMessage()]);
}
?>