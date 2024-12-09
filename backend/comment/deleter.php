<!-- {
    "id": 1
} -->

<?php
require_once '../auth.php';
require_once '../auditrecord.php';
header('Content-Type: application/json');

try{
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;

    if($id){
        $db = new Database();
        $connection = $db->getConnection();
        $auth = new Auth($connection);
        $auth->checkAuth($input);

        $query = "DELETE FROM comment WHERE id = :id";
        $stmt = $connection->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt -> execute();

        echo json_encode(['message' => 'Comment deleted successfully!']);
    }else{
        $audit = new Audit($connection);
        $audit->record($input['local_storage_user_id'], 'DELETE', "Error in delete.php", $_SERVER['REMOTE_ADDR']);
        http_response_code(400);
        echo json_encode(['message'=> 'ID is required.']);
    }
}catch(Exception $e){
    $audit = new Audit($connection);
    $audit->record($input['local_storage_user_id'] ?? null, 'DELETE', $e->getMessage(), $_SERVER['REMOTE_ADDR']);
    http_response_code(500);
    echo json_encode(['message'=>'Error deleting category.',
    'error'=>$e->getMessage()]);
}
?>