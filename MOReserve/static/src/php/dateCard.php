<?php
include "conn.php";

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input["number"])) {
    echo json_encode(["success" => false, "message" => "Card number is required."]);
    exit;
}

$number = $input["number"]; 
$newLimit = $input["date"];

$stmt = $db->prepare("UPDATE cards SET date = ? WHERE number = ?");

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Database query preparation failed."]);
    exit;
}

$stmt->bind_param("ss", $newLimit, $number); 

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Card status updated to blocked."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update card status."]);
}

$stmt->close();
?>
