<?php
require '../display_error.php';
require '../cors.php';
require '../auth.php';
require_once '../auditrecord.php'; 

header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);

try {
    $db = new Database();
    $connection = $db->getConnection();

    $auth = new Auth($connection);
    $validUserId = $auth->checkAuth($input);

    $audit = null;
    if ($connection) {
        $audit = new Audit($connection);
    }

    if ($validUserId) {
        $sql = "SELECT r.*, u.username, GROUP_CONCAT(DISTINCT c.name) as categories, GROUP_CONCAT(DISTINCT i.name) as ingredients
        FROM recipe r 
        JOIN users u ON r.created_by = u.id 
        LEFT JOIN recipe_categories rc ON r.id = rc.recipe_id
        LEFT JOIN categories c ON rc.category_id = c.id
        LEFT JOIN ingredient i ON r.id = i.recipe_id
        WHERE r.created_by = :validUserId
        GROUP BY r.id, u.username";

        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':validUserId', $validUserId, PDO::PARAM_INT);
        $stmt->execute();
        $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($recipes) {
            echo json_encode($recipes);
        } else {
            if ($audit) {
                $audit->record($_GET['user_id'] ?? null, 'GET', "No recipes found for user: $validUserId", $_SERVER['REMOTE_ADDR']);
            }
            http_response_code(404);
            echo json_encode(['message' => 'No recipes found']);
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
