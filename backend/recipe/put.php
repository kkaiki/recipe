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

        // 필드가 있을 경우 업데이트할 쿼리 준비
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


        // 이미지 업데이트가 있을 경우 처리
        if (isset($data['image']) && !empty($data['image'])) {
            // 이미지 Base64 디코딩
            $image = $data['image'];
            if ($image === false) {
                echo json_encode(["message" => "Invalid Base64 image data"]);
                http_response_code(400);
                exit;
            }
            $fields[] = "image = ?";
            $params[] = $image;
            $types .= 'b';  // 바이너리 데이터
        }

        // 업데이트할 필드가 있다면
        if (count($fields) > 0) {
            $sql = "UPDATE recipe SET " . implode(", ", $fields) . " WHERE id = ?";
            $params[] = $id;
            $types .= 'i';  // ID는 정수형이므로 'i' 추가

            print_r($sql."<br>");
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param($types, ...$params);

            // 쿼리 실행
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
