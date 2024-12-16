<?php
require '../cors.php';
require '../connect.php'; 
require_once '../auditrecord.php'; 

header('Content-Type: application/json');

try {
    $id = $_GET['id'] ?? null;
    $db = new Database();
    $connection = $db->getConnection();

    $audit = null;
    if ($connection) {
        $audit = new Audit($connection);
    }

    if ($id) {
        $query = "
            SELECT r.*, u.username, GROUP_CONCAT(DISTINCT c.name) as categories, GROUP_CONCAT(DISTINCT i.name) as ingredients
            FROM recipe r 
            JOIN users u ON r.created_by = u.id 
            LEFT JOIN recipe_categories rc ON r.id = rc.recipe_id
            LEFT JOIN categories c ON rc.category_id = c.id
            LEFT JOIN ingredient i ON r.id = i.recipe_id
            WHERE r.id = :id
            GROUP BY r.id, u.username
        ";
        $stmt = $connection->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($recipe) {
            echo json_encode($recipe);
        } else {
            if ($audit) {
                $audit->record($_GET['user_id'] ?? null, 'GET', "Recipe not found for ID: $id", $_SERVER['REMOTE_ADDR']);
            }
            http_response_code(404);
            echo json_encode(['message' => 'Recipe not found']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'idがありません']);
    }
} catch (PDOException $e) {
    if (isset($audit)) {
        $audit->record($_GET['user_id'] ?? null, 'ERROR', $e->getMessage(), $_SERVER['REMOTE_ADDR']);
    }
    http_response_code(500);
    echo json_encode([
        'message' => 'Error fetching recipes.',
        'error' => $e->getMessage()
    ]);
}
?>
