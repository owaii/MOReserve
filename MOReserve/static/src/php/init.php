<?php
header('Content-Type: application/json');

$db = new mysqli('localhost', 'root', '', 'more');

// Check if the connection was successful
if ($db->connect_error) {
    echo json_encode(["error" => "Database connection failed: " . $db->connect_error]);
    exit;
}

// Get the ID from the query string and validate it
$id = $_GET["id"] ?? null;
if (!$id || !is_numeric($id)) {
    echo json_encode(["error" => "Invalid or missing ID"]);
    exit;
}

// Prepare the SQL query
$stmt = $db->prepare("
    SELECT 
        users.user_id AS id, 
        users.username AS username, 
        users.email AS email, 
        users.password_hash AS passwordHash, 
        users.phone_number AS phoneNum, 
        users.created_at AS createdAt, 
        users.last_login AS lastLogin, 
        user_details.pesel AS pesel, 
        user_details.mothers_maiden_name AS mName, 
        user_details.country AS country, 
        user_details.city AS city, 
        user_details.street AS street, 
        user_details.building_number AS buildingNum, 
        user_details.apartment_number AS apNum, 
        user_details.postal_code AS postal, 
        accounts.balance AS balance, 
        cards.card_number AS cardNum, 
        cards.expiration_date AS expDate, 
        cards.cardholder_name AS cardHolderName, 
        cards.status AS status 
    FROM users 
    JOIN user_details ON users.user_id = user_details.user_id 
    JOIN accounts ON users.user_id = accounts.user_id 
    JOIN cards ON users.user_id = cards.user_id 
    WHERE users.user_id = ?
");

if (!$stmt) {
    echo json_encode(["error" => "Failed to prepare statement: " . $db->error]);
    exit;
}

// Bind the parameter and execute the query
$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    echo json_encode(["error" => "Failed to execute query: " . $stmt->error]);
    exit;
}

// Get the result and send the data as JSON
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    echo json_encode($data);
} else {
    echo json_encode(["error" => "No user found with this ID"]);
}

$stmt->close();
$db->close();
?>
