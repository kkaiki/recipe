<?php
$servername="localhost";
$username="root";
$password="mysql";
$dbname = "recipe_db";


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, name, description, created_at FROM recipe";  
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row["id"] . "<br>";
        echo "Recipe Name: " . $row["name"] . "<br>";
        echo "Description: " . $row["description"] . "<br>";
        echo "Created At: " . $row["created_at"] . "<br>";
        echo "<hr>"; 
    }
} else {
    echo "No results found";
}

$conn->close();
?>
