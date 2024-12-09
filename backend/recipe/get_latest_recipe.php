<?php
$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "recipe_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, name, description, is_active, created_by, created_at, image
        FROM recipe
        ORDER BY created_at DESC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $recipes = array();
    while ($row = $result->fetch_assoc()) {
        if ($row['image']) {
            // 예: 이미지 형식이 jpeg일 경우
            $row['image'] = base64_encode($row['image']);
            
            // MIME 타입을 적절히 설정하여 출력
            echo '<img src="data:image/jpeg;base64,' . $row['image'] . '" alt="Recipe Image" /><br>';
        }
        $recipes[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($recipes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(array("message" => "No recipes found"), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>
