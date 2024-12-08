<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "recipe_db";

// MySQL 연결
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        echo "Invalid JSON data";
        http_response_code(400);
        exit;
    }

    if (isset($data['id'])) {
        $id = $data['id'];
        $fields = [];
        $params = [];
        $types = '';

        if (isset($data['name'])) {
            $fields[] = "name = ?";
            $params[] = $data['name'];
            $types .= 's';
        }
        if (isset($data['description'])) {
            $fields[] = "description = ?";
            $params[] = $data['description'];
            $types .= 's';
        }
        if (isset($data['is_active'])) {
            $fields[] = "is_active = ?";
            $params[] = $data['is_active'];
            $types .= 'i';
        }
        if (isset($data['created_by'])) {
            $fields[] = "created_by = ?";
            $params[] = $data['created_by'];
            $types .= 'i';
        }

        if (count($fields) > 0) {
            $sql = "UPDATE recipe SET " . implode(", ", $fields) . " WHERE id = ?";
            $params[] = $id;
            $types .= 'i';

            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    http_response_code(200);
                    echo json_encode(["message" => "Recipe updated successfully"]);
                } else {
                    http_response_code(404);
                    echo json_encode(["message" => "Recipe not found"]);
                }
            } else {
                echo "Execution failed: " . $stmt->error;
                http_response_code(500);
            }
            $stmt->close();
        } else {
            http_response_code(400);
            echo json_encode(["message" => "No fields to update"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Missing required field: id"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
}

$conn->close();
?>
