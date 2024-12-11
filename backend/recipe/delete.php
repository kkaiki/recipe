<!-- 
parameters:
- id

requirements:
- check user is authenticated
- check user is owner of recipe

response:
nothing(only 200 http status code)

-->


<?php
$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "recipe_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM recipe WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                http_response_code(200); 
                echo json_encode(["message" => "Recipe deleted successfully"]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Recipe not found"]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Error deleting recipe"]);
        }

        $stmt->close();
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Error preparing SQL statement"]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Missing required parameter: id"]);
}

$conn->close();
?>
