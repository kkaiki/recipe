<?php
//http://localhost/recipe/backend/recipe/post.php
$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "recipe_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['name'], $data['description'], $data['is_active'], $data['created_by'])) {
        $name = $data['name'];
        $description = $data['description'];
        $is_active = $data['is_active'];
        $created_by = $data['created_by'];
        $created_at = date('Y-m-d H:i:s'); 

        $sql = "INSERT INTO recipe (name, description, is_active, created_by, created_at) 
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiss", $name, $description, $is_active, $created_by, $created_at);

        if ($stmt->execute()) {
            http_response_code(201); 
            echo json_encode(array(
                "message" => "Recipe created successfully",
                "id" => $stmt->insert_id
            ));
        } else {
            http_response_code(500); 
            echo json_encode(array("message" => "Error creating recipe"));
        }

        $stmt->close();
    } else {
        http_response_code(400); // HTTP 400 Bad Request
        echo json_encode(array("message" => "Invalid input data"));
    }
} else {
    http_response_code(405); // HTTP 405 Method Not Allowed
    echo json_encode(array("message" => "Method not allowed"));
}

$conn->close();
?>
