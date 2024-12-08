<?php
$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "recipe_db";

// MySQL 연결
$conn = new mysqli($servername, $username, $password, $dbname);

// 연결 확인
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// GET 파라미터 받기
$name = isset($_GET['name']) ? trim($_GET['name']) : null;
$description = isset($_GET['description']) ? trim($_GET['description']) : null;

// 기본 쿼리
$query = "SELECT id, name, description, is_active, created_by, created_at, image FROM recipe";

// 조건 배열
$conditions = [];

// 조건 추가
if ($name) {
    $conditions[] = "name LIKE ?";
}
if ($description) {
    $conditions[] = "description LIKE ?";
}

// WHERE 절이 필요한 경우 조건 추가
if (count($conditions) > 0) {
    $query .= " WHERE " . implode(" OR ", $conditions);  // OR 조건을 사용하여 name 또는 description에서 검색
}

// 쿼리 준비
$stmt = $conn->prepare($query);

// 파라미터 바인딩
$params = [];
$types = '';  // 타입 정보

// name 파라미터 처리
if ($name) {
    $params[] = "%" . $name . "%";  // LIKE 검색을 위한 값
    $types .= 's';  // string 타입
}

// description 파라미터 처리
if ($description) {
    $params[] = "%" . $description . "%";  // LIKE 검색을 위한 값
    $types .= 's';  
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$recipes = [];
while ($row = $result->fetch_assoc()) {
    $recipes[] = $row;
}

header('Content-Type: application/json');
echo json_encode($recipes);

$stmt->close();
$conn->close();
?>
