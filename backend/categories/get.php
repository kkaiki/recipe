<!-- to get category -->
<?php
 require '../connect.php';
 require_once '../auditrecord.php';
 header('Content-Type: application/json');

 try{
    $db = new Database();
    $connection = $db -> getConnection();
    $audit = new Audit($connection);
    //$_GET : 슈퍼 글로벌 배열... url에서 쿼리스트링으로 받은거 읽음.(?뒤에있는거)
    $id = $_GET['id'] ?? null;

    if ($id){
        //특정 ID의 카테고리 조회함..
        $query = "SELECT * FROM categories WHERE id = :id";
        $stmt = $connection->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        //fetch() : 반환 데이터 형식 설정 가능.
        //PDO::FETCH_ASSOC 이면 결과를 연관배열로 반환함. key는 컬럼이름...
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        //그래서 변수 category가 참이면(있으면) $category를 반환함... 
        if($category){
            echo json_encode($category); //결과 반환
        } else{
            //없으면 404반환
            $audit->record($_GET['user_id'] ?? null, 'GET', "Category not found for ID: $id", $_SERVER['REMOTE_ADDR']);
            http_response_code(404);
            echo json_encode(['message' => 'Category not found']);
        }
    }  else {
        //모든 카테고리를 조회함. --> 클라이언트가 전체 카테고리 목록 요청할때..
        $query = "SELECT * FROM categories";
        $stmt = $connection->query($query);
        $categories = $stmt ->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($categories);
    }
    } catch(PDOException $e){
        $audit = new Audit($connection ?? null); // $connection이 없을 경우 null 처리
        $audit->record($_GET['user_id'] ?? null, 'ERROR', $e->getMessage(), $_SERVER['REMOTE_ADDR']);
        http_response_code(500);
        echo json_encode(['message' => 'Error fetching categories.',
        'error' => $e->getMessage()]);
 }