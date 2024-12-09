<?php
header('Content-Type: application/json');

if (!isset($_GET["value"]) || !isset($_GET["id"])) {
    echo json_encode(['status' => 'error', 'valid' => false, 'message' => 'Invalid data']);
    exit;
}

$isValid = true;

$db = new mysqli("localhost", "root", "", "more");

if ($db->connect_error) {
    error_log("Connection failed: " . $db->connect_error);
    echo json_encode(['status' => 'error', 'valid' => false, 'message' => 'An internal error occurred.']);
    exit;
}

$value = $db->real_escape_string($_GET["value"]);
$id = $db->real_escape_string($_GET["id"]);

$sql = "SELECT 1 FROM users WHERE $id = ? LIMIT 1";
$stmt = $db->prepare($sql);

if (!$stmt) {
    error_log("Query preparation failed: " . $db->error);
    echo json_encode(['status' => 'error', 'valid' => false, 'message' => 'An internal error occurred.']);
    exit;
}

$stmt->bind_param('s', $value);
$stmt->execute();
$result = $stmt->get_result();

$isValid = ($result->num_rows === 0);

$stmt->close();
$db->close();

echo json_encode([
    'status' => 'success',
    'valid' => $isValid,
]);
