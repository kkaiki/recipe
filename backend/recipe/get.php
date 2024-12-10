<?php
require '../connect.php'; // Database connection
require_once '../auditrecord.php'; // Audit class

header('Content-Type: application/json');

try {
    $db = new Database();
    $connection = $db->getConnection();
    $audit = new Audit($connection);

    // 쿼리 실행
    $query = "SELECT id, name, description, created_at, image FROM recipe";
    $stmt = $connection->query($query);
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($recipes)) {
        foreach ($recipes as $recipe) {
            echo "ID: " . htmlspecialchars($recipe["id"]) . "<br>";
            echo "Recipe Name: " . htmlspecialchars($recipe["name"]) . "<br>";
            echo "Description: " . htmlspecialchars($recipe["description"]) . "<br>";
            echo "Created At: " . htmlspecialchars($recipe["created_at"]) . "<br>";

            if (!empty($recipe["image"])) {
                // 이미지 데이터 처리
                if (strpos($recipe["image"], '/') !== false) {
                    // 이미지가 파일 경로인 경우
                    echo '<img src="' . htmlspecialchars($recipe["image"]) . '" alt="Recipe Image" /><br>';
                } else {
                    // 이미지가 바이너리 데이터인 경우
                    $imageData = base64_encode($recipe["image"]);
                    echo '<img src="data:image/jpeg;base64,' . $imageData . '" alt="Recipe Image" /><br>';
                }
            } else {
                echo "No image available.<br>";
            }

            echo "<hr>";
        }
    } else {
        echo "No results found.";
    }
} catch (PDOException $e) {
    // 에러 발생 시 audit 기록 및 500 응답 반환
    $audit->record($_GET['user_id'] ?? null, 'ERROR', $e->getMessage(), $_SERVER['REMOTE_ADDR']);
    http_response_code(500);
    echo json_encode([
        'message' => 'Error fetching recipes.',
        'error' => $e->getMessage()
    ]);
}
