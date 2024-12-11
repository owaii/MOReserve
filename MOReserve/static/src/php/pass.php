<?php
$db = new mysqli("localhost", "root", "", "more");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$input = json_decode(file_get_contents("php://input"), true);
$id = intval($input['id'] ?? 0);
$currentPassword = $input['currentPassword'] ?? '';
$newPassword = $input['newPassword'] ?? '';

if ($id <= 0 || empty($currentPassword) || empty($newPassword)) {
    echo json_encode(["success" => false, "error" => "Invalid input parameters."]);
    $db->close();
    exit;
}

$stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (password_verify($currentPassword, $row["password"])) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(["success" => true, "message" => "Password updated successfully."]);
    } else {
        echo json_encode(["success" => false, "error" => "Incorrect current password."]);
    }
} else {
    echo json_encode(["success" => false, "error" => "User not found."]);
}

$db->close();
?>
