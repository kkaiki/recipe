<?php
header('Content-Type: application/json');
require_once '../auth.php'; 
require_once '../auditrecord.php'; 

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $db = new Database();
    $connection = $db->getConnection();

    $auth = new Auth($connection);
    $auth->checkAuth($input);  // 인증을 위한 메소드 호출

    $id = $input['id'] ?? null;

    if (!$id) {
        $audit = new Audit($connection);
        $audit->record(
            $input['local_storage_user_id'] ?? null, 'DELETE', "ID is missing in delete request", $_SERVER['REMOTE_ADDR']
        );
        http_response_code(400);
        echo json_encode(['message' => 'ID is required.']);
        exit;
    }

    $checkQuery = "SELECT * FROM recipe WHERE id = :id";
    $checkStmt = $connection->prepare($checkQuery);
    $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
    $checkStmt->execute();
    $recipe = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$recipe) {
        $audit = new Audit($connection);
        $audit->record(
            $input['local_storage_user_id'] ?? null, 'DELETE', "Recipe ID not found for ID: $id", $_SERVER['REMOTE_ADDR']
        );
        http_response_code(404);
        echo json_encode(['message' => 'Recipe ID not found.']);
        exit;
    }

    $deleteQuery = "DELETE FROM recipe WHERE id = :id";
    $deleteStmt = $connection->prepare($deleteQuery);
    $deleteStmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($deleteStmt->execute()) {
        http_response_code(200);
        echo json_encode(['message' => 'Recipe deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Error deleting recipe']);
    }

} catch (PDOException $e) {
    $audit = new Audit($connection);
    $audit->record($input['local_storage_user_id'] ?? null, 'DELETE', $e->getMessage(), $_SERVER['REMOTE_ADDR']);
    http_response_code(500);
    echo json_encode(['message' => 'Error deleting recipe.', 'error' => $e->getMessage()]);
}
?>
