<?php
$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "recipe_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = isset($_GET['name']) ? trim($_GET['name']) : null;
$description = isset($_GET['description']) ? trim($_GET['description']) : null;

$query = "SELECT id, name, description, is_active, created_by, created_at, image FROM recipe";

$conditions = [];

if ($name) {
    $conditions[] = "name LIKE ?";
}
if ($description) {
    $conditions[] = "description LIKE ?";
}

if (count($conditions) > 0) {
    $query .= " WHERE " . implode(" OR ", $conditions);  
}

$stmt = $conn->prepare($query);

$params = [];
$types = '';  

if ($name) {
    $params[] = "%" . $name . "%";
    $types .= 's';  
}

if ($description) {
    $params[] = "%" . $description . "%";  
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
