<?php include "conn.php";
$data = array();

$input = json_decode(file_get_contents("php://input"), true);
$id = 8;

if ($id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid user ID provided."]);
    exit;
}

$stmt = $db->prepare("
    SELECT f.userID as userID, f.friendID as friendID, f.transactions as transactions, 
           users.icon as icon, users.name as name, users.surname as surname 
    FROM friends f 
    JOIN users ON f.friendID = users.id 
    WHERE f.userID = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

$id = [];
$fullName = [];
$icon = [];
$transactions = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        array_push($id, $row["friendID"]);
        array_push($fullName, $row["name"]. " ". $row["surname"]);
        array_push($icon, $row["icon"]);
        array_push($transactions, $row["transactions"]);
    }
    echo json_encode(["success" => true, "id" => $id, "fullName" => $fullName, "icon" => $icon, "transactions" => $transactions]);
} else {
    echo json_encode(["success" => false, "message" => "No contacts found for the given userID"]);
}

$db->close();
?>
