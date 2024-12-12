<!-- to update category -->
 <?php
require_once '../auth.php';
require_once '../auditrecord.php';
header('Content-Type: application/json');

 try{
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id']?? null;
    $name = $input['name']?? null;
    $image = $input['image'] ?? null;

    $db = new Database();
    $connection = $db->getConnection();
    $auth = new Auth($connection);
    $auth->checkAuth($input);

    // ID가 제공되었는지 확인
    if (!$id) {
        $audit = new Audit($connection);
        $audit->record(
            $input['local_storage_user_id'] ?? null, 'UPDATE', "ID is missing in update request", $_SERVER['REMOTE_ADDR']
        );
        http_response_code(400);
        echo json_encode(['message' => 'ID is required.']);
        exit;
    }
    // ID가 존재하는지 select 문으로 확인..
    $checkQuery = "SELECT COUNT(*) FROM categories WHERE id = :id";
    $checkStmt = $connection->prepare($checkQuery);
    $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
    $checkStmt->execute();
    $count = $checkStmt->fetchColumn();

    if ($count == 0) {
        $audit = new Audit($connection);
        $audit->record(
        $input['local_storage_user_id'] ?? null, 'UPDATE', "Category ID not found for ID: $id", $_SERVER['REMOTE_ADDR']        
    );
        http_response_code(404);
        echo json_encode(['message' => 'Category ID not found.']);
        exit;
    }
    //만약에 아이디가 존재하고, name혹은 image가 존재한다면...
    if ($name || $image) {
        $query = "UPDATE categories SET ";
        $fields=[];
        if ($name) $fields[] = "name = :name";
        if ($image){
            if ($image === "null") {
                $fields[] = "image = NULL"; // 명시적으로 NULL 값 처리
            } else {
                $fields[] = "image = :image";
            }
        }
        //implode(): 배열의 요소를 하나의 문자열로 결합
        //fields["name", "image"] --> "name, image"
        // .= : 현재 문자열 끝에 새로운 문자열을 추가함 그러니까, set 뒤에 where 어쩌고가 붙음.
        $query .= implode(', ', $fields) . " WHERE id = :id";


        $stmt = $connection->prepare($query);
        if ($name) $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        if ($image !== null && $image !== "null") $stmt->bindParam(':image', $image, PDO::PARAM_LOB);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt -> execute();

        echo json_encode(['message' => 'Category updated successfully.']);
        } else {
            $audit = new Audit($connection);
            $audit->record($input['local_storage_user_id'], 'UPDATE', "Error in update.php", $_SERVER['REMOTE_ADDR']);
            http_response_code(400);
            echo json_encode(['message'=> 'ID and at least one field to update are required.']);
        }
    } catch(Exception $e){
        $audit = new Audit($connection);
        $audit->record($input['local_storage_user_id'] ?? null, 'UPDATE', $e->getMessage(), $_SERVER['REMOTE_ADDR']);
        http_response_code(500);
        echo json_encode(['message' => 'Error updating category.', 'error'=>$e->getMessage()]);
        echo json_encode(['query' => $query]);
    }
 ?>
