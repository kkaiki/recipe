<!-- {
    "name": "tomato 200g",
    "recipe_id": 5
} -->

<?php
    require_once '../auth.php';
    require_once '../auditrecord.php';
    header('Content-Type: application/json');

    try{
        $input = json_decode(file_get_contents('php://input'),true);
        $recipeId = $input['recipe_id'] ?? null;
        $name = $input['name'] ?? null;

        $db = new Database();
        $connection = $db -> getConnection();
        $auth = new Auth($connection);
        $auth -> checkAuth($input);

        if($recipeId && $name){
            $query = "INSERT INTO ingredient (recipe_id, name) VALUES (:recipe_id, :name)";
            $stmt = $connection -> prepare($query);
            $stmt -> bindParam(':recipe_id', $recipeId, PDO::PARAM_INT);
            $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
            $stmt -> execute();

            
            http_response_code(201);
            echo json_encode([
                'message' => 'Ingredient created successfully.',
                'name' => $name // 요청에서 받은 name 값 반환
            ]);
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
        echo json_encode(['message'=>'Error creating ingredients.',
        'error'=>$e->getMessage()]);
    }
?>