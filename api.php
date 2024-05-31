<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";  // Adjust if necessary
$password = "";      // Adjust if necessary
$dbname = "mydatabase";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $sql = "SELECT * FROM users WHERE id=$id";
            $result = $conn->query($sql);
            $data = $result->fetch_assoc();
        } else {
            $sql = "SELECT * FROM users";
            $result = $conn->query($sql);
            $data = $result->fetch_all(MYSQLI_ASSOC);
        }
        echo json_encode($data);
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        $name = $conn->real_escape_string($input['name']);
        $email = $conn->real_escape_string($input['email']);
        $phone_number = $conn->real_escape_string($input['phone_number']);
        $sql = "INSERT INTO users (name, email, phone_number) VALUES ('$name', '$email', '$phone_number')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["message" => "New record created successfully", "id" => $conn->insert_id]);
        } else {
            echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
        }
        break;

    case 'PUT':
        $input = json_decode(file_get_contents('php://input'), true);
        $id = intval($input['id']);
        $name = $conn->real_escape_string($input['name']);
        $email = $conn->real_escape_string($input['email']);
        $phone_number = $conn->real_escape_string($input['phone_number']);
        $sql = "UPDATE users SET name='$name', email='$email', phone_number='$phone_number' WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["message" => "Record updated successfully"]);
        } else {
            echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
        }
        break;

    case 'DELETE':
        $input = json_decode(file_get_contents('php://input'), true);
        $id = intval($input['id']);
        $sql = "DELETE FROM users WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["message" => "Record deleted successfully"]);
        } else {
            echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
        }
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
        break;
}

$conn->close();
?>
