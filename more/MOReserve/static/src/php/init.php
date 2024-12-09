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
        users.userId AS id, 
        users.username AS username, 
        users.name AS name,
        users.surname AS surname,
        users.email AS email, 
        users.passwordHash AS passwordHash, 
        users.phoneNumber AS phoneNumber, 
        users.createdAt AS createdAt, 
        users.lastLogin AS lastLogin, 
        users.pesel AS pesel, 
        user_details.mothersMaidenName AS mName, 
        user_details.country AS country, 
        user_details.city AS city, 
        user_details.street AS street, 
        user_details.buildingNumber AS buildingNumber, 
        user_details.apartmentNumber AS apNumber, 
        user_details.postalCode AS postal, 
        accounts.balance AS balance, 
        cards.cardNumber AS cardNumber, 
        cards.expirationDate AS expDate, 
        cards.cardholderName AS cardHolderName, 
        cards.status AS status 
        cards.cvv as cvv
    FROM users 
    JOIN user_details ON users.userId = user_details.userId 
    JOIN accounts ON users.userId = accounts.userId 
    JOIN cards ON users.userId = cards.userId 
    WHERE users.userId = ?
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