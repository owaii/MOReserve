<?php
include "conn.php";

// Decode input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input["id"]) || !is_numeric($input["id"])) {
    echo json_encode(["success" => false, "message" => "Invalid or missing user ID provided."]);
    exit;
}

$id = $input["id"];

if ($id <= 0) {
    echo json_encode(["success" => false, "message" => "User ID must be a positive integer."]);
    exit;
}

$stmt = $db->prepare("
    SELECT number, date, holderName, cvv, status, created, limits 
    FROM cards 
    WHERE userID = ?;
");

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Database query preparation failed."]);
    exit;
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if (!$result) {
    echo json_encode(["success" => false, "message" => "Database query execution failed."]);
    exit;
}

$number = [];
$date = [];
$holderName = [];
$cvv = [];
$status = [];
$created = [];
$limits = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Fetch the raw date
        $rawDate = $row["created"];

        $x = new DateTime($rawDate);
        $formattedDate = $x->format('m/y');
        
        $number[] = $row["number"];
        $date[] = $row["date"]; // Store the formatted date
        $holderName[] = $row["holderName"];
        $cvv[] = $row["cvv"];
        $status[] = $row["status"];
        $created[] = $formattedDate;
        $limits[] = $row["limits"];
    }

    // Return the result in JSON format
    echo json_encode([
        "success" => true,
        "number" => $number,
        "date" => $date,
        "name" => $holderName,
        "created" => $created
    ]);
} else {
    echo json_encode(["success" => false, "message" => "No contacts found for the given user ID."]);
}

$db->close();
?>
