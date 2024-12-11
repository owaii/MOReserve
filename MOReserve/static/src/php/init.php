<?php
header('Content-Type: application/json');

$db = new mysqli('localhost', 'root', '', 'more');

// Check if the connection was successful
if ($db->connect_error) {
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    exit;
}

// Get the ID from the query string and validate it
$id = $_GET["id"] ?? null;

if (!$id || !is_numeric($id)) {
    echo json_encode(["success" => false, "error" => "Invalid or missing ID"]);
    exit;
}

// Prepare the SQL query
$stmt = $db->prepare("
    SELECT 
        users.id AS id, 
        users.username AS username, 
        users.name AS name,
        users.surname AS surname,
        users.email AS email, 
        users.password AS password, 
        users.phoneNumber AS phoneNumber, 
        users.created AS createdAt, 
        users.login AS lastLogin, 
        users.pesel AS pesel, 
        personal.mothersMaidenName AS mName, 
        personal.country AS country, 
        personal.city AS city, 
        personal.street AS street, 
        personal.buildingNumber AS buildingNumber, 
        personal.apartmentNumber AS apNumber, 
        personal.postal AS postal, 
        users.balance AS balance, 
        cards.number AS cardNumber, 
        cards.date AS expDate, 
        cards.holderName AS cardHolderName, 
        cards.status AS status,
        cards.cvv AS cvv
    FROM users 
    LEFT JOIN personal ON users.personalID = personal.id
    LEFT JOIN cards ON users.id = cards.userId
    WHERE users.id = ?;
");

if (!$stmt) {
    echo json_encode(["success" => false, "error" => "Failed to prepare statement"]);
    exit;
}

// Bind the parameter and execute the query
$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    echo json_encode(["success" => false, "error" => "Failed to execute query"]);
    exit;
}

// Get the result and send the data as JSON
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    echo json_encode(["success" => true, "data" => $data]);
} else {
    echo json_encode(["success" => false, "error" => "No user found with this ID"]);
}

$stmt->close();
$db->close();
?>
