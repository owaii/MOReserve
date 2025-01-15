<?php
include "conn.php";

// Decode input
$input = json_decode(file_get_contents('php://input'), true);

// Check if 'number' is set in the input
if (!isset($input["number"])) {
    echo json_encode(["success" => false, "message" => "Card number is required."]);
    exit;
}

$number = $input["number"];  // Card number passed from the input

// Prepare the query to update the card status
$stmt = $db->prepare("UPDATE cards SET status = 'block' WHERE number = ?");

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Database query preparation failed."]);
    exit;
}

// Bind the card number parameter as a string ('s' instead of 'i')
$stmt->bind_param("s", $number); 

// Execute the query
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Card status updated to blocked."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update card status."]);
}

$stmt->close();
?>
