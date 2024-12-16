<?php
require '../cors.php';
require_once '../auth.php';
require_once '../auditrecord.php';  
header('Content-Type: application/json');

try {
    $db = new Database();
    $connection = $db->getConnection();
    $input = json_decode(file_get_contents('php://input'), true);

    $name = $input['name'] ?? null;
    $description = $input['description'] ?? null;
    $is_active = $input['is_active'] ?? null;
    $created_by = $input['created_by'] ?? null;
    $image = $input['image'] ?? null;

    $auth = new Auth($connection);
    $auth->checkAuth($input);

    if (!isset($name, $description, $is_active, $created_by, $image)) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid input. Required fields: name, description, is_active, created_by, image.']);
        exit;
    }

    $query = "INSERT INTO recipe (name, description, is_active, created_by, image) VALUES (:name, :description, :is_active, :created_by, :image)";
    $stmt = $connection->prepare($query);  
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':description', $description, PDO::PARAM_STR);
    $stmt->bindParam(':is_active', $is_active, PDO::PARAM_INT);
    $stmt->bindParam(':created_by', $created_by, PDO::PARAM_INT);
    $stmt->bindParam(':image', $image, PDO::PARAM_STR); 
    $stmt->execute(); 

    http_response_code(201);
    echo json_encode(['message' => 'Recipe created successfully.', 'id' => $connection->lastInsertId()]);

} catch (PDOException $e) {
    $audit = new Audit($connection);
    $audit->record($input['local_storage_user_id'] ?? null, 'ERROR', $e->getMessage(), $_SERVER['REMOTE_ADDR']);
    http_response_code(500);
    echo json_encode(['message' => 'Error creating recipe.', 'error' => $e->getMessage()]);
}
?>
