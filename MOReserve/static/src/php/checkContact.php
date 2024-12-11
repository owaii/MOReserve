<?php
header('Content-Type: application/json'); // Set the correct header

// Database connection
$db = new mysqli("localhost", "root", "", "more");

$val = (int)$_GET["val"];
$userID = $_GET["id"];

$stmt = $db->prepare("
    SELECT id FROM users WHERE phoneNumber = ?
");
$stmt->bind_param("i", $val);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc(); // Fetch the row
    $id = $row["id"];

    // Check if the user is already a friend
    $stmt = $db->prepare("
        SELECT * FROM friends WHERE userID = ? AND friendID = ?;
    ");
    $stmt->bind_param("ii", $userID, $id);
    $stmt->execute();
    $result = $stmt->get_result(); // Get the result before closing the statement
    $stmt->close();

    if ($result->num_rows > 0) {
        echo json_encode(["success" => false, "error" => "User already added"]);
        exit;
    }

    // Add the user as a friend
    $stmt = $db->prepare("
        INSERT INTO friends (userID, friendID, transactions) 
        VALUES (?, ?, 0)
    ");
    $stmt->bind_param("ii", $userID, $id);
    $stmt->execute();
    $stmt->close();

    // Return the updated list of friends
    $stmt = $db->prepare("
        SELECT f.userID as userID, f.friendID as friendID, f.transactions as transactions, 
               users.icon as icon, users.name as name, users.surname as surname 
        FROM friends f 
        JOIN users ON f.userID = users.id 
        WHERE f.userID = ?
    ");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result(); // Get the result before closing the statement
    $stmt->close();

    $friends = [];
    while ($row = $result->fetch_assoc()) {
        $friends[] = $row;
    }

    // Return the result as JSON
    echo json_encode(["success" => true, "data" => $friends]);
    exit;
} else {
    // Return an error response as JSON if the user with the phone number does not exist
    echo json_encode(["success" => false, "error" => "No user found with this phoneNumber"]);
    exit;
}
?>
