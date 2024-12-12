<?php
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
        $query = "SELECT * FROM recipe WHERE id = :id";
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
        $query = 'SELECT * FROM recipe';
        $stmt = $connection->prepare($query);
        $stmt->execute();
        $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($recipes);
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
