<?php
$db = new mysqli("localhost", "root", "", "more");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$input = json_decode(file_get_contents("php://input"), true);
$id = intval($input['id'] ?? 0);
$newEmail = $input['newEmail'] ?? '';

if ($id <= 0 || empty($newEmail)) {
    echo json_encode(["success" => false, "error" => "Invalid input parameters."]);
    $db->close();
    exit;
}

$stmt = $db->prepare("SELECT email FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows > 0) {
    $stmt = $db->prepare("UPDATE users SET email = ? WHERE id = ?");
    $stmt->bind_param("si", $newEmail, $id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(["success" => true, "message" => "Email updated successfully."]);
} else {
    echo json_encode(["success" => false, "error" => "User not found."]);
}

$db->close();
?>
