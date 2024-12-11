<?php
$db = new mysqli("localhost", "root", "", "more");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$input = json_decode(file_get_contents("php://input"), true);

$id = intval($input['id'] ?? 0);

$login = date("Y-m-d");

$stmt = $db->prepare("UPDATE users SET login = ? WHERE id = ?");
$stmt->bind_param("si", $login, $id); 
$stmt->execute();
$stmt->close();

echo json_encode(["success" => true]);
exit;
?>
