<!-- to delete category -->
 <?php
 require '../connect.php';
 header('Content-Type: application/json');

 try{
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;

    if($id){
        $db = new Database();
        $connection = $db->getConnection();

        $query = "DELETE FROM categories WHERE id = :id";
        $stmt = $connection -> prepare($query);
        $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
        $stmt -> execute();

        echo json_encode(['message' => 'Category deleted successfully.']);
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'ID is required.']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Error deleting category.', 'error' => $e->getMessage()]);
}
 ?>