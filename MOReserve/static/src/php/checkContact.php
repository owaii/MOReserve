<?php
$db = new mysqli("localhost", "root", "", "more");

if ($db->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $db->connect_error]));
}

$input = json_decode(file_get_contents("php://input"), true);
$id = intval($input['id'] ?? 0);
$value = strval($input['val'] ?? '');

if (empty($value)) {
    echo json_encode(["success" => false, "message" => "Phone number is required."]);
    exit;
}

$stmt = $db->prepare("SELECT id FROM users WHERE phoneNumber = ?");
$stmt->bind_param("s", $value);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $friendID = $row["id"];

    $stmt = $db->prepare("SELECT * FROM friends WHERE userID = ? AND friendID = ?");
    $stmt->bind_param("ii", $id, $friendID);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "User already added"]);
        exit;
    }

    $stmt = $db->prepare("INSERT INTO friends (userID, friendID, transactions) VALUES (?, ?, 0)");
    $stmt->bind_param("ii", $id, $friendID);
    $stmt->execute();
    $stmt->close();

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

    $friends = [];
    while ($row = $result->fetch_assoc()) {
        $friends[] = $row;
    }

    echo json_encode(["success" => true, "data" => $friends]);
    exit;
} else {
    echo json_encode(["success" => false, "message" => "No user found with the given phone number"]);
}
?>
