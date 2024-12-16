<?php
require '../cors.php';
header('Content-Type: application/json');
require_once '../auth.php'; 
require_once '../auditrecord.php'; 

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $db = new Database();
    $connection = $db->getConnection();
    $auth = new Auth($connection);
    $auth->checkAuth($input);
    
    $id = $input['id'] ?? null;
    $description = $input['description']?? null;
    $name = $input['name']?? null;
    $image = $input['image'] ?? null;

    

    if (!$id) {
        $audit = new Audit($connection);
        $audit->record(
            $input['local_storage_user_id'] ?? null, 'UPDATE', "ID is missing in update request", $_SERVER['REMOTE_ADDR']
        );
        http_response_code(400);
        echo json_encode(['message' => 'ID is required.']);
        exit;
    }
    $checkQuery = "SELECT COUNT(*) FROM recipe WHERE id = :id";
    $checkStmt = $connection->prepare($checkQuery);
    $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
    $checkStmt->execute();
    $count = $checkStmt->fetchColumn();

    if ($count == 0) {
        $audit = new Audit($connection);
        $audit->record(
        $input['local_storage_user_id'] ?? null, 'UPDATE', "Recipe ID not found for ID: $id", $_SERVER['REMOTE_ADDR']        
    );
        http_response_code(404);
        echo json_encode(['message' => 'Recipe ID not found.']);
        exit;
    }


    if($name || $description || $image) {
        $query = "UPDATE recipe SET ";
        $fields=[];
        if ($name) $fields[] = "name = :name";
        if ($description) $fields[] = "description = :description";
        if ($image) $fields[] = "image = :image";

        $query .= implode(", ", $fields) . " WHERE id = :id";
        $stmt = $connection->prepare($query);
        if ($id) $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        if ($name) $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        if ($image) $stmt->bindParam(':image', $image, PDO::PARAM_STR);
        if ($description) $stmt->bindParam(':description', $description, PDO::PARAM_STR);

        $stmt->execute();

        echo json_encode(['message' => 'Recipe updated successfully.']);

    } else {
        $audit = new Audit($connection);
        $audit->record($input['local_storage_user_id'], 'DELETE', "Error in delete.php", $_SERVER['REMOTE_ADDR']);
        http_response_code(400);
        echo json_encode(['message'=> 'ID and at least one field to update are required.']);
    }

    
    
} catch(PDOException $e) {
    $audit = new Audit($connection);
    $audit->record($input['local_storage_user_id'] ?? null, 'DELETE', $e->getMessage(), $_SERVER['REMOTE_ADDR']);
    http_response_code(500);
    echo json_encode(['message' => 'Error updating category.', 'error'=>$e->getMessage()]);
    echo json_encode(['query' => $query]);

}
?>
