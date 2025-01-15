<?php 
include("conn.php");

// Get the input data and decode it from JSON format
$input = json_decode(file_get_contents('php://input'), true);

// Validate input parameters
if (!isset($input['id']) || !isset($input['name'])) {
    echo json_encode(["success" => false, "message" => "Missing required parameters"]);
    exit();
}

$id = $input["id"];
$name = $input["name"];
$date = date("n/y", strtotime("+5 years")); // Set expiry date 5 years from now
$cvv = random_int(100, 999); // Generate a random CVV

// Generate a unique card number
do {
    $number = random_int(7000000000000000, 7999999999999999);
    $stmtCardNumber = $db->prepare("SELECT number FROM cards WHERE number = ?");
    $stmtCardNumber->bind_param("s", $number);
    $stmtCardNumber->execute();
    $stmtCardNumber->store_result();
} while ($stmtCardNumber->num_rows > 0);

// Insert the new card into the database
$stmtCard = $db->prepare("INSERT INTO cards (userID, number, date, holderName, cvv, status, limits) VALUES (?, ?, ?, ?, ?, ?, ?)");
$defaultLimits = 100;
$status = 'active';
$stmtCard->bind_param("issssis", $id, $number, $date, $name, $cvv, $status, $defaultLimits);

if ($stmtCard->execute()) {
    echo json_encode(["success" => true, "message" => "Card added successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to add card"]);
}
?>
