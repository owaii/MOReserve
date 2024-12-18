<?php
$db = new mysqli("localhost", "root", "", "more");

if ($db->connect_error) {
    die(json_encode(["success" => false, "error" => "Database connection failed: " . $db->connect_error]));
}

$input = json_decode(file_get_contents("php://input"), true);
if (!is_array($input)) {
    echo json_encode(["success" => false, "error" => "Invalid JSON input."]);
    exit;
}

$id = intval($input['id'] ?? 0);
$newEmail = $input['newEmail'] ?? '';

if ($id <= 0 || empty($newEmail)) {
    echo json_encode(["success" => false, "error" => "Invalid input parameters."]);
    $db->close();
    exit;
}

$stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $newEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["success" => false, "error" => "That email already exists."]);
    $stmt->close();
    $db->close();
    exit;
}
$stmt->close();

$stmt = $db->prepare("SELECT email FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $stmt->close();
    $stmt = $db->prepare("UPDATE users SET email = ? WHERE id = ?");
    $stmt->bind_param("si", $newEmail, $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Email updated successfully."]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to update email."]);
    }
} else {
    echo json_encode(["success" => false, "error" => "User not found."]);
}

$stmt->close();
$db->close();
?>
