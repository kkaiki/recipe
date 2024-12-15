<?php
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

    $auth = new Auth($connection);
    $auth->checkAuth($input);

    
    if (!isset($input['name'], $input['description'], $input['is_active'], $input['created_by'], $input['image'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid input. Required fields: name, description, is_active, created_by, image.']);
        exit;
    }
    if(($name && $description && $is_active && $created_by) || $image ) {
        $query = "INSERT INTO recipe (name, description, is_active, created_by, image) VALUES (:name, :description, :is_active, :created_by, :image)";
        $stmt = $connection->prepare($query);  
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':is_active', $is_active, PDO::PARAM_INT);
        $stmt->bindParam(':created_by', $created_by, PDO::PARAM_STR);
        $stmt->bindParam(':image', $image, PDO::PARAM_STR); 
        $stmt->execute(); 
        http_response_code(201);
        echo json_encode(['message' => 'Recipe created successfully.', 'id' => $connection->lastInsertId()]);
    } else {
        $audit = new Auth($connection);
        $audit->record($input['local_storage_user_id'] ?? null, 'DELETE', $e->getMessage(), $_SERVER['REMOTE_ADDR']);
        http_response_code(400);
        echo json_encode(['message' => 'name, description, required.', 'error' => $e->getMessage()]);
    }

} catch (PDOException $e) {
    $audit = new Audit($connection);
    $audit->record($input['local_storage_user_id'] ?? null, 'DELETE', $e->getMessage(), $_SERVER['REMOTE_ADDR']);
    http_response_code(500);
    echo json_encode(['message' => 'Error updating category.', 'error'=>$e->getMessage()]);
    echo json_encode(['query' => $query]);
}

?>