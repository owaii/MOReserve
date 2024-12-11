<?php 
$data = array();

$db = new mysqli("localhost", "root", "", "more");

if ($db->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed: " . $db->connect_error]));
}

$input = json_decode(file_get_contents("php://input"), true);
$id = intval($input['id'] ?? 0);

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

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'id' => $row['friendID'],
            'name' => $row['name'] . ' ' . $row['surname'],
            'pfp' => "static/img/users/pfp/" . ($row['icon'] ?: 'astrid.webp'),
            'transactions' => $row['transactions']
        ];
    }
    echo json_encode(["success" => true, "data" => $data]);
} else {
    echo json_encode(["success" => false, "message" => "No contacts found for the given userID"]);
}

$db->close();
?>
