<?php
$servername="localhost";
$username="root";
$password="mysql";
$dbname = "recipe_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, name, description, created_at, image FROM recipe";  
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row["id"] . "<br>";
        echo "Recipe Name: " . $row["name"] . "<br>";
        echo "Description: " . $row["description"] . "<br>";
        echo "Created At: " . $row["created_at"] . "<br>";

        // 이미지 데이터가 있는 경우
        if ($row["image"]) {
            // base64로 인코딩하여 이미지 출력
            $imageData = base64_encode($row["image"]);
            echo '<img src="data:image/jpeg;base64,' . $imageData . '" alt="Recipe Image" /><br>';
        } else {
            echo "No image available.<br>";
        }
        
        echo "<hr>"; 
    }
} else {
    echo "No results found";
}

$conn->close();
?>
