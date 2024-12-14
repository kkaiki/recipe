<?php
    require_once '../auth.php';
    require_once '../auditrecord.php';
    header('Content-Type: application/json'); //json파일로 응답받음.

 try{
    //json 요청 데이터 파싱
    //php://input : php에서 제공하는 입력스트림...
    //프론트에서 데이터를 요청 본문에 담아서 서버로 보내는데, 그걸 php://input을 사용해 요청 본문을 읽어옴.
    $input = json_decode(file_get_contents('php://input'),true);
    //input['name'] 값이 존재하면, 그 값을 사용하고, 아니면 null 반환
    $name = $input['name'] ?? null;
    //image를 longblob으로 받는데, binarydata를 저장할 수 있음. --> 이미지, 비디오, pdf, 오디오파일 등...
    $image = $input['image'] ?? null; 

    $db = new Database();
        $connection = $db -> getConnection();
        $auth = new Auth($connection);
        $auth->checkAuth($input);

    if($name && $image){
        $query = "INSERT INTO categories (name, image) VALUES (:name, :image)";
        //prepare 메소드 : PDO에서 제공하는 기능, sql 인젝션 방지, 효율적인 쿼리 실행 위해서
        //따라서, stmt는 prepare에 쿼리문을 넣어진 것을 변수 stmt에 넣은것
        $stmt = $connection -> prepare($query);
        //blindParam 메소드 : sql 쿼리의 플레이스 홀더에 값을 레퍼런스로 연결함.
        //PDO::PARAM_STR : 문자열 데이터를 sql 쿼리의 플레이스 홀더에 바인딩하겠다.
        $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
        $stmt -> bindParam(':image', $image, PDO::PARAM_LOB);
        //execute() : 준비된 쿼리를 실행해서 데이터베이스에 반영함.
        $stmt -> execute();

        http_response_code(201); //이게 살행되면, 성공되었다고 뜨게..
        //json_encode : php에서 제이슨 형식으로 데이터를 출력하는것...
        echo json_encode(['message' => 'Category created successfully.']);
    } else{
        $audit = new Audit($connection);
        $audit->record($input['local_storage_user_id'], 'DELETE', "Error in delete.php", $_SERVER['REMOTE_ADDR']);
        http_response_code(400); //400 : 잘못된 요청
        echo json_encode(['message' => 'Name and image are required.']);
    }
} catch(Exception $e){
    $audit = new Audit($connection);
    $audit->record($input['local_storage_user_id'] ?? null, 'DELETE', $e->getMessage(), $_SERVER['REMOTE_ADDR']);
    http_response_code(500); //500 : 서버 내부 오류
    echo json_encode(['message'=>'Error creating category.',
    'error'=>$e->getMessage()]);
}

 ?>