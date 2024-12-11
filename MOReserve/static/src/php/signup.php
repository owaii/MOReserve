<?php
// Database connection
$db = new mysqli('localhost', 'root', '', 'more');

// Check if the connection was successful
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Retrieve POST data
$data = json_decode(file_get_contents("php://input"), true);

$username = $data['username'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
$phoneNum = $data['phoneNum'] ?? '';
$pesel = $data['pesel'] ?? '';
$mName = $data['mName'] ?? '';
$country = $data['country'] ?? '';
$city = $data['city'] ?? '';
$street = $data['street'] ?? '';
$bdNum = $data['buldingNum'] ?? '';
$apNum = $data['apNum'] ?? '';
$postal = $data['postal'] ?? '';
$name = $data['name'] ?? '';
$surname = $data['surname'] ?? '';

// Hash the password
$hashedPass = password_hash($password, PASSWORD_DEFAULT);

// Begin a transaction
$db->begin_transaction();

try {
    // Insert into the `personal` table
    $stmtPersonal = $db->prepare("
        INSERT INTO personal (mothersMaidenName, country, city, street, buildingNumber, apartmentNumber, postal)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmtPersonal->bind_param("sssssss", $mName, $country, $city, $street, $bdNum, $apNum, $postal);
    $stmtPersonal->execute();

    // Get the inserted personal ID
    $personalID = $db->insert_id;
    
    // Insert into the `users` table
    $stmtUsers = $db->prepare("
        INSERT INTO users (personalID, username, email, name, surname, pesel, password, phoneNumber)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmtUsers->bind_param("isssssss", $personalID, $username, $email, $name, $surname, $pesel, $hashedPass, $phoneNum);
    $stmtUsers->execute();

    $userID = $db->insert_id;
    $date = date("n/y");
    $cvv = random_int(100,999);
    $fullName = $name . " " . $surname;

    $stmtCardNumber = $db->prepare("SELECT MAX(number) AS maxNumber FROM cards");
    $stmtCardNumber->execute();
    $result = $stmtCardNumber->get_result();
    $row = $result->fetch_assoc();
    $number = ($row['maxNumber'] ?? 6999999999999999) + 1;
    $stmtCardNumber->close();

    $stmtCard = $db->prepare("
        INSERT INTO cards (userID, number, date, holderName, cvv, status)
        VALUES (?, ?, ?, ?, ?,'active')
    ");
    $stmtCard->bind_param("iissi", $userID, $number, $date, $fullName, $cvv);
    $stmtCard->execute();

    $db->commit();

    echo json_encode(["success" => true, "message" => "User created successfully."]);
} catch (Exception $e) {
    // Rollback the transaction on error
    $db->rollback();
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
} finally {
    // Close database connection
    $stmtUsers->close();
    $stmtPersonal->close();
    $stmtCard->close();
    $db->close();
}
