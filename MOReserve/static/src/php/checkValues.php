<?php
header('Content-Type: application/json');

// Ensure value and id are set
if (!isset($_GET["value"]) || !isset($_GET["id"])) {
    echo json_encode(['status' => 'error', 'valid' => false, 'message' => 'Invalid data']);
    exit;
}

$value = $_GET["value"];
$id = $_GET["id"];

$isValid = true;

$db = new mysqli("localhost", "root", "", "more");

// Check DB connection
if ($db->connect_error) {
    error_log("Connection failed: " . $db->connect_error);
    echo json_encode(['status' => 'error', 'valid' => false, 'message' => 'An internal error occurred.']);
    exit;
}

// Escape value to prevent SQL Injection
$value = $db->real_escape_string($value);

// Prepare SQL query with dynamic column name safely
$sql = "SELECT 1 FROM users WHERE $id = ? LIMIT 1";
$stmt = $db->prepare($sql);

// Check if preparation succeeded
if (!$stmt) {
    error_log("Query preparation failed: " . $db->error);
    echo json_encode(['status' => 'error', 'valid' => false, 'message' => 'An internal error occurred.']);
    exit;
}

// Bind parameters and execute the query
$stmt->bind_param('s', $value);
$stmt->execute();
$result = $stmt->get_result();

// Check if a matching record was found
$isValid = ($result->num_rows === 0);

// Close statement and DB connection
$stmt->close();
$db->close();

// Return JSON response
echo json_encode([
    'status' => 'success',
    'valid' => $isValid,
]);
?>
