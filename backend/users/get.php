<!-- 
to get user data from the database

expected parameters:
- username
- password

check:
- check authenication

return:
- id
- username
- first_name
- last_name
- email
- profile
- role
- liked recipes
- created recipes
-->
<?php
$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "recipe_db";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<p>DB 연결에 성공했습니다.</p>";

    // Prepared Statement를 사용한 데이터 삽입
    $sql = "INSERT INTO users (username, first_name, last_name, email, password, profile, role) 
    VALUES (:username, :first_name, :last_name, :email, :password, :profile, :role)";

    $stmt = $conn->prepare($sql);

    // 바인딩 값 설정
    foreach ($data as $row) {
        $stmt->bindParam(':username', $row['username']);
        $stmt->bindParam(':first_name', $row['first_name']);
        $stmt->bindParam(':last_name', $row['last_name']);
        $stmt->bindParam(':email', $row['email']);
        $stmt->bindParam(':password', $row['password']);
        $stmt->bindParam(':profile', $row['profile'], PDO::PARAM_LOB); // BLOB 데이터는 특별히 처리
        $stmt->bindParam(':role', $row['role']);
        $stmt->execute();
    }
    echo "데이터가 성공적으로 삽입되었습니다.";
} catch (PDOException $e) {
    echo "데이터 삽입 실패: " . $e->getMessage();
}

// 연결 해제
$conn = null;
?>