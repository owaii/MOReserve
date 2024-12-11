<?php 
$data = array();

header('Content-Type: application/json'); // Set the correct header

// Database connection
$db = new mysqli("localhost", "root", "", "more");

// Check for connection errors
if ($db->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed: " . $db->connect_error]));
}

$userID = $_GET["id"];

$stmt = $db->prepare("
    SELECT f.userID as userID, f.friendID as friendID, f.transactions as transactions, 
           users.icon as icon, users.name as name, users.surname as surname 
    FROM friends f 
    JOIN users ON f.friendID = users.id 
    WHERE f.userID = ?
");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        array_push($data, $row); // Populate the $data array with each row
    }
    echo json_encode(["success" => true, "data" => $data]);
} else {
    echo json_encode(["success" => false, "message" => "No friends found for the given userID"]);
}

// Close the database connection
$db->close();
?>
