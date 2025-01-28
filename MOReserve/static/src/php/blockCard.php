<?php
include "conn.php";

// Decode input
$input = json_decode(file_get_contents('php://input'), true);

// Check if 'number' is set in the input
if (!isset($input["number"])) {
    echo json_encode(["success" => false, "message" => "Card number is required."]);
    exit;
}

$number = preg_replace('/\s+/', '', $input["number"]);
$status = $input["txt"];

// Prepare the query to update the card status
$stmt = $db->prepare("UPDATE cards SET `status` = ? WHERE number = ?");
$stmt->bind_param("ss", $status, $number); 

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Database query preparation failed."]);
    exit;
}

// Execute the query
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Card status updated to blocked."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update card status."]);
}

$stmt->close();
?>
