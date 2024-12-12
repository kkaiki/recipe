<?php
header('Content-Type: application/json');
require_once '../auth.php'; 
require_once '../auditrecord.php'; 

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $db = new Database();
    $connection = $db->getConnection();
    $auth = new Auth($connection);
    $auth->checkAuth($input);
    
    $id = $input['id'] ?? null;
    $description = $input['description']?? null;
    $name = $input['name']?? null;
    $image = $input['image'] ?? null;

    

    if (!$id) {
        $audit = new Audit($connection);
        $audit->record(
            $input['local_storage_user_id'] ?? null, 'UPDATE', "ID is missing in update request", $_SERVER['REMOTE_ADDR']
        );
        http_response_code(400);
        echo json_encode(['message' => 'ID is required.']);
        exit;
    }
    $checkQuery = "SELECT COUNT(*) FROM recipe WHERE id = :id";
    $checkStmt = $connection->prepare($checkQuery);
    $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
    $checkStmt->execute();
    $count = $checkStmt->fetchColumn();

    if ($count == 0) {
        $audit = new Audit($connection);
        $audit->record(
        $input['local_storage_user_id'] ?? null, 'UPDATE', "Recipe ID not found for ID: $id", $_SERVER['REMOTE_ADDR']        
    );
        http_response_code(404);
        echo json_encode(['message' => 'Recipe ID not found.']);
        exit;
    }


    if($name || $description || $image) {
        $query = "UPDATE recipe SET ";
        $fields=[];
        if ($name) $fields[] = "name = :name";
        if ($description) $fields[] = "description = :description";
        if ($image) $fields[] = "image = :image";

        $query .= implode(", ", $fields) . " WHERE id = :id";
        $stmt = $connection->prepare($query);
        if ($id) $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        if ($name) $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        if ($image) $stmt->bindParam(':image', $image, PDO::PARAM_STR);
        if ($description) $stmt->bindParam(':description', $description, PDO::PARAM_STR);

        $stmt->execute();

        echo json_encode(['message' => 'Recipe updated successfully.']);

    } else {
        $audit = new Audit($connection);
        $audit->record($input['local_storage_user_id'], 'DELETE', "Error in delete.php", $_SERVER['REMOTE_ADDR']);
        http_response_code(400);
        echo json_encode(['message'=> 'ID and at least one field to update are required.']);
    }

    
    
} catch(PDOException $e) {
    $audit = new Audit($connection);
    $audit->record($input['local_storage_user_id'] ?? null, 'DELETE', $e->getMessage(), $_SERVER['REMOTE_ADDR']);
    http_response_code(500);
    echo json_encode(['message' => 'Error updating category.', 'error'=>$e->getMessage()]);
    echo json_encode(['query' => $query]);

}
// MySQL 연결
// $conn = new mysqli($servername, $username, $password, $dbname);

// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

// if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
//     $data = json_decode(file_get_contents('php://input'), true);

//     if (!$data) {
//         echo "Invalid JSON data";
//         http_response_code(400);
//         exit;
//     }

//     if (isset($data['id'])) {
//         $id = $data['id'];
//         $fields = [];
//         $params = [];
//         $types = '';

//         // 필드가 있을 경우 업데이트할 쿼리 준비
//         if (isset($data['name'])) {
//             $fields[] = "name = ?";
//             $params[] = $data['name'];
//             $types .= 's';
//         }
//         if (isset($data['description'])) {
//             $fields[] = "description = ?";
//             $params[] = $data['description'];
//             $types .= 's';
//         }


//         // 이미지 업데이트가 있을 경우 처리
//         if (isset($data['image']) && !empty($data['image'])) {
//             // 이미지 Base64 디코딩
//             $image = $data['image'];
//             if ($image === false) {
//                 echo json_encode(["message" => "Invalid Base64 image data"]);
//                 http_response_code(400);
//                 exit;
//             }
//             $fields[] = "image = ?";
//             $params[] = $image;
//             $types .= 'b';  // 바이너리 데이터
//         }

//         // 업데이트할 필드가 있다면
//         if (count($fields) > 0) {
//             $sql = "UPDATE recipe SET " . implode(", ", $fields) . " WHERE id = ?";
//             $params[] = $id;
//             $types .= 'i';  // ID는 정수형이므로 'i' 추가

//             print_r($sql."<br>");
//             $stmt = $conn->prepare($sql);
//             if (!$stmt) {
//                 die("Prepare failed: " . $conn->error);
//             }

//             $stmt->bind_param($types, ...$params);

//             // 쿼리 실행
//             if ($stmt->execute()) {
//                 if ($stmt->affected_rows > 0) {
//                     http_response_code(200);
//                     echo json_encode(["message" => "Recipe updated successfully"]);
//                 } else {
//                     http_response_code(404);
//                     echo json_encode(["message" => "Recipe not found"]);
//                 }
//             } else {
//                 echo "Execution failed: " . $stmt->error;
//                 http_response_code(500);
//             }
//             $stmt->close();
//         } else {
//             http_response_code(400);
//             echo json_encode(["message" => "No fields to update"]);
//         }
//     } else {
//         http_response_code(400);
//         echo json_encode(["message" => "Missing required field: id"]);
//     }
// } else {
//     http_response_code(405);
//     echo json_encode(["message" => "Method not allowed"]);
// }

// $conn->close();
?>
