<!-- to update category -->
 <?php
 require '../connect.php';
 header('Content-Type : application/json');

 try{
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id']?? null;
    $name = $input['name']?? null;
    $image = $image['image']?? null;

    //만약에 아이디가 존재하고, name혹은 image가 존재한다면...
    if ($id && ($name || $image)){
        $db = new Database();
        $connection = $db->getConnection();

        $query = "UPDATE categories SET";
        $fields=[];
        if ($name) $fields[] = "name = :name";
        if ($image) $fields[] = "image = :image";
        //implode(): 배열의 요소를 하나의 문자열로 결합
        //fields["name", "image"] --> "name, image"
        // .= : 현재 문자열 끝에 새로운 문자열을 추가함 그러니까, set 뒤에 where 어쩌고가 붙음.
        $query .= implode(', ', $fields) . " WHERE id = :id";

        $stmt = $connection->prepare($query);
        if ($name) $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        if ($image) $stmt->bindParam(':image', $image, PDO::PARAM_LOB);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt -> execute();

        echo json_encode(['message' => 'Category updated successfully.']);
        } else {
            http_response_code(400);
            echo json_encode(['message'=> 'ID and at least one field to update are required.']);
        }
    } catch(Exception $e){
        http_response_code(500);
        echo json_encode(['message' => 'Error updating category.', 'error'=>$e->getMessage()]);
    }
 ?>
