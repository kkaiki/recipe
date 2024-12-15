<?php
require '../connect.php'; 
require_once '../auditrecord.php'; 
header('Content-Type: application/json');

try {
    $db = new Database();
    $connection = $db->getConnection();

    $audit = null;
    if ($connection) {
        $audit = new Audit($connection);
    }

    $name = isset($_GET['q']) ? trim($_GET['q']) : null;
    $category_id = isset($_GET['category_id']) ? trim($_GET['category_id']) : null;

    $query = "SELECT r.* FROM recipe r
                JOIN recipe_categories rc ON r.id = rc.recipe_id
                WHERE (r.name LIKE '%$name%' OR r.description LIKE '%$name%')
                AND ('$category_id' = '' OR rc.category_id = '$category_id')";

    $stmt = $connection->prepare($query);
    

    $stmt->execute();
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($recipes);

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
