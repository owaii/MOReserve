<?php

// Database credentials
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'more';


    // Create a connection to the database
    $db = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

    // Check if the connection was successful
    if ($db->connect_error) {
        throw new Exception("Database connection failed: " . $db->connect_error);
    }

    // Retrieve values from the URL (default to empty string if not set)
    $username = $_GET['username'] ?? '';
    $email = $_GET['email'] ?? '';
    $password = $_GET['pass'] ?? '';
    $phoneNum = $_GET['phoneNum'] ?? '';
    $pesel = $_GET['pesel'] ?? '';
    $mName = $_GET['mName'] ?? '';
    $country = $_GET['country'] ?? '';
    $city = $_GET['city'] ?? '';
    $street = $_GET['street'] ?? '';
    $bdNum = $_GET['bdNum'] ?? '';
    $apNum = $_GET['apNum'] ?? '';
    $postal = $_GET['postal'] ?? '';

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $createdAt = date('Y-m-d');
    $lastLogin = $createdAt;

    // Start transaction
    $db->begin_transaction();

    // Insert user into the `users` table
    $stmt = $db->prepare("
        INSERT INTO users (username, email, password_hash, phone_number, created_at, last_login)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    if (!$stmt) {
        throw new Exception("Failed to prepare user query: " . $db->error);
    }

    $stmt->bind_param(
        "ssssss",
        $username, $email, $hashedPassword, $phoneNum, $createdAt, $lastLogin
    );

    if (!$stmt->execute()) {
        throw new Exception("Error adding user: " . $stmt->error);
    }

    $userId = $db->insert_id; // Get the newly created user's ID
    $stmt->close();

    // Insert user details into the `user_details` table
    $stmtDetails = $db->prepare("
        INSERT INTO user_details (user_id, pesel, mothers_maiden_name, country, city, street, building_number, apartment_number, postal_code)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    if (!$stmtDetails) {
        throw new Exception("Failed to prepare user details query: " . $db->error);
    }

    $stmtDetails->bind_param(
        "issssssss",
        $userId, $pesel, $mName, $country, $city, $street, $bdNum, $apNum, $postal
    );

    if (!$stmtDetails->execute()) {
        throw new Exception("Error adding user details: " . $stmtDetails->error);
    }
    $stmtDetails->close();

    // Insert initial account for the user
    $balance = 0;
    $stmtAccount = $db->prepare("
        INSERT INTO accounts (user_id, balance, created_at)
        VALUES (?, ?, ?)
    ");
    if (!$stmtAccount) {
        throw new Exception("Failed to prepare account query: " . $db->error);
    }

    $stmtAccount->bind_param("iis", $userId, $balance, $createdAt);

    if (!$stmtAccount->execute()) {
        throw new Exception("Error adding account: " . $stmtAccount->error);
    }
    $stmtAccount->close();

    // Insert data into the `cards` table
    // Step 1: Generate a unique card number
    $result = $db->query("SELECT card_number FROM cards ORDER BY card_id DESC LIMIT 1");
    $lastCardNumber = $result->fetch_assoc()['card_number'] ?? '7000000000'; // Default start

    $cardNumber = (int)$lastCardNumber + 1; // Increment the last card number
    while (substr((string)$cardNumber, 0, 1) !== '7') {
        $cardNumber++; // Ensure it starts with 7
    }

    // Step 2: Generate expiration date (5 years from today)
    $expirationDate = date('Y-m-d', strtotime('+5 years'));

    // Step 3: Set cardholder name (placeholder for now)
    $cardholderName = "Default Name";

    // Insert the card
    $stmtCard = $db->prepare("
        INSERT INTO cards (user_id, card_number, expiration_date, cardholder_name, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    if (!$stmtCard) {
        throw new Exception("Failed to prepare card query: " . $db->error);
    }

    $status = 'active'; // Default status
    $stmtCard->bind_param(
        "iissss",
        $userId, $cardNumber, $expirationDate, $cardholderName, $status, $createdAt
    );

    if (!$stmtCard->execute()) {
        throw new Exception("Error adding card: " . $stmtCard->error);
    }
    $stmtCard->close();

    $stmtDash = $db->prepare("
        INSERT INTO dashboard_data (user_id, monthly_spending, monthly_income) VALUES (?,0,0);
    ");

    $stmtDash->bind_param(
        "i",
        $userId
    );

    if (!$stmtDash) {
        throw new Exception("Failed to prepare card query: " . $db->error);
    }

    if (!$stmtDash->execute()) {
        throw new Exception("Error adding account: " . $stmtAccount->error);
    }

    // Commit the transaction
    $db->commit();

    echo "User, details, account, and card created successfully!";
    header("Location: ../../../login.html");

