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

    $name = isset($_GET['name']) ? trim($_GET['name']) : null;
    $description = isset($_GET['description']) ? trim($_GET['description']) : null;

    $query = "SELECT * FROM recipe";
    $conditions = [];

    if ($name) {
        $conditions[] = "name LIKE :name";
    }
    if ($description) {
        $conditions[] = "description LIKE :description";
    }

    if (count($conditions) > 0) {
        $query .= " WHERE " . implode(" OR ", $conditions);
    }

    $stmt = $connection->prepare($query);

    if ($name) {
        $stmt->bindValue(':name', "%" . $name . "%", PDO::PARAM_STR);
    }
    if ($description) {
        $stmt->bindValue(':description', "%" . $description . "%", PDO::PARAM_STR);
    }

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
