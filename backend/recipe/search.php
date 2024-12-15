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

    $query = "SELECT * FROM recipe where name like '%$name%' or description like '%$name%'";

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
