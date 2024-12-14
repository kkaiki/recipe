<?php
//  require_once '../connect.php';
 require_once '../auth.php';
 require '../auditrecord.php';
//  require '../display_error';
 header('Content-Type: application/json');

 try{
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;

    $db = new Database();
    $connection = $db->getConnection();

    $auth = new Auth($connection);
    $auth->checkAuth($input);

    if($id){
        
        // 1. 참조 데이터 삭제 -> foreign key
        $query1 = "DELETE FROM recipe_categories WHERE category_id = :id";
        $stmt1 = $connection->prepare($query1);
        $stmt1->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt1->execute();

        // 2. 카테고리 삭제
        $query2 = "DELETE FROM categories WHERE id = :id";
        $stmt2 = $connection->prepare($query2);
        $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt2->execute();
        echo json_encode(['message' => 'Category deleted successfully.']);
    } else {
        $audit = new Audit($connection);
        $audit->record($input['local_storage_user_id'], 'DELETE', "Error in delete.php", $_SERVER['REMOTE_ADDR']);
        http_response_code(400);
        echo json_encode(['message' => 'ID is required.']);
    }
} catch (Exception $e) {
    $audit = new Audit($connection);
    $audit->record($input['local_storage_user_id'] ?? null, 'DELETE', $e->getMessage(), $_SERVER['REMOTE_ADDR']);
    error_log("Error in delete.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Error deleting category.', 'error' => $e->getMessage()]);
}
 ?>