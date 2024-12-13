<?php
header('Content-Type: application/json');
require '../connect.php';
require_once '../auditrecord.php';

try {
    $db = new Database();
    $connection = $db->getConnection();
    $audit = new Audit($connection);

    $query = "SELECT * FROM recipe ORDER BY created_at DESC";
    $stmt = $connection->query($query);
    $stmt->execute();

    $latest = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($latest) {
        echo json_encode($latest);
    } else {
        if ($audit) {
            $audit->record($_GET['user_id'] ?? null, 'GET', "Latest Recipe not found for ID: $id", $_SERVER['REMOTE_ADDR']);
        }
        http_response_code(404);
        echo json_encode(['message' => 'Latest Recipe not found']);
    }
} catch(PDOException $e) {
    if (isset($audit)) {
        $audit->record($_GET['user_id'] ?? null, 'ERROR', $e->getMessage(), $_SERVER['REMOTE_ADDR']);
    }
    http_response_code(500);
    echo json_encode([
        'message' => 'Error fetching Latest recipes.',
        'error' => $e->getMessage()
    ]);
}

?>
