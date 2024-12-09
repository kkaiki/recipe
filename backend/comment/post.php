<!-- to create comment -->
<!-- {댓글 내용, 작성자, 레시피id
    "comment": "This recipe is amazing!",
    "recipe_id": 3,
    "created_by": 7
} -->
<?php
    require_once '../auth.php';
    require_once '../auditrecord.php';
    header('Content-Type: application/json');

    try{
        $input = json_decode(file_get_contents('php://input'),true);
        $recipeId = $input['recipe_id'] ?? null;
        $comment = $input['comment'] ?? null;
        $createdBy = $input['created_by'] ?? null;

        $db = new Database();
        $connection = $db -> getConnection();
        $auth = new Auth($connection);
        $auth -> checkAuth($input);
        
        if($recipeId && $comment && $createdBy){
            $query = "INSERT INTO comment (recipe_id, comment, created_by) VALUES (:recipe_id, :comment, :created_by)";
            $stmt = $connection -> prepare($query);
            $stmt -> bindParam(':recipe_id', $recipeId, PDO::PARAM_INT);
            $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt -> bindParam(':created_by', $createdBy, PDO::PARAM_INT);
            $stmt -> execute();

            http_response_code(201);
            echo json_encode(['message' => 'Comment created successfully.']);
        }else{
            $audit = new Audit($connection);
            $audit->record($input['local_storage_user_id'], 'INSERT', "Error in post.php", $_SERVER['REMOTE_ADDR']);
            http_response_code(400); //400 : 잘못된 요청
            echo json_encode(['message' => 'recipe_id, comment and author are required.']);
        }
    } catch(Exception $e){
        $audit = new Audit($connection);
        $audit->record($input['local_storage_user_id'] ?? null, 'INSERT', $e->getMessage(), $_SERVER['REMOTE_ADDR']);
        http_response_code(500); //500 : 서버 내부 오류
        echo json_encode(['message'=>'Error creating comments.',
        'error'=>$e->getMessage()]);
    }
?>