<?php
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

    if (isset($data['name'], $data['description'], $data['is_active'], $data['created_by'], $data['image'])) {
        $name = $data['name'];
        $description = $data['description'];
        $is_active = $data['is_active'];
        $created_by = $data['created_by'];
        $created_at = date('Y-m-d H:i:s');
        
        $image = $data['image'];
        if ($image === false) {
            http_response_code(400); // HTTP 400 Bad Request
            echo json_encode(array("message" => "Invalid Base64 image data"));
            exit();
        }

        $sql = "INSERT INTO recipe (name, description, is_active, created_by, created_at, image) 
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            http_response_code(500); // HTTP 500 Internal Server Error
            echo json_encode(array("message" => "Failed to prepare statement"));
            exit();
        }

        $stmt->bind_param("ssissb", $name, $description, $is_active, $created_by, $created_at, $image);
        
        $stmt->send_long_data(5, $image);

        if ($stmt->execute()) {
            http_response_code(201); // HTTP 201 Created
            echo json_encode(array(
                "message" => "Recipe created successfully",
                "id" => $stmt->insert_id
            ));
        } else {
            http_response_code(500); // HTTP 500 Internal Server Error
            echo json_encode(array("message" => "Error creating recipe: " . $stmt->error));
        }

        $stmt->close();
    } else {
        http_response_code(400); // HTTP 400 Bad Request
        echo json_encode(array("message" => "Invalid input data. Required fields: name, description, is_active, created_by, image."));
    }
} else {
    http_response_code(405); // HTTP 405 Method Not Allowed
    echo json_encode(array("message" => "Method not allowed. Use POST."));
}

$conn->close();
?>
