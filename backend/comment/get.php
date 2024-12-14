<?php
require_once '../connect.php'; 
require_once '../auditrecord.php'; 
header('Content-Type: application/json');

try {
    $db = new Database();
    $connection = $db->getConnection();
    $audit = new Audit($connection);

    $recipeId = $_GET['recipe_id'] ?? null;

    if ($recipeId) {
        $query = "SELECT * FROM comment WHERE recipe_id = :recipe_id";
        $stmt = $connection->prepare($query);
        $stmt->bindParam(':recipe_id', $recipeId, PDO::PARAM_INT);

        error_log("Executing query: $query with recipe_id = $recipeId"); // 쿼리 로그

        $stmt->execute();
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($comments) {
            echo json_encode(['message' => 'Comments fetched successfully.', 'data' => $comments]);
        } else {
            $audit->record($_GET['user_id'] ?? null, 'GET', "No comments found for recipe ID: $recipeId", $_SERVER['REMOTE_ADDR']);
            http_response_code(404);
            echo json_encode(['message' => 'No comments found.']);
        }
    } else {
        // 모든 댓글 조회
        $query = "SELECT * FROM comment";
        $stmt = $connection->query($query);

        error_log("Executing query: $query");

        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['message' => 'All comments fetched successfully.', 'data' => $comments]);
    }
} catch (PDOException $e) {
    $audit->record($_GET['user_id'] ?? null, 'ERROR', $e->getMessage(), $_SERVER['REMOTE_ADDR']);
    http_response_code(500);
    echo json_encode(['message' => 'Error fetching comments.', 'error' => $e->getMessage()]);
}
?>
