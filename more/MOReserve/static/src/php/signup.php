<?php

    $db = new mysqli("localhost", "root", "", "more");

    if ($db->connect_error) {
        throw new Exception("Database connection failed: " . $db->connect_error);
    }

    $username = $_GET['username'] ?? '';
    $name = $_GET['name'] ?? '';
    $surname = $_GET['surname'] ?? '';
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

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $createdAt = date('Y-m-d');
    $lastLogin = $createdAt;

    $db->begin_transaction();

    $stmt = $db->prepare("INSERT INTO users (username, name, surname, pesel, email, passwordHash, phoneNumber, createdAt, lastLogin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $username, $name, $surname, $pesel, $email, $hashedPassword, $phoneNum, $createdAt, $lastLogin);
    if (!$stmt->execute()) {
        throw new Exception("Error adding user: " . $stmt->error);
    }

    $userId = $db->insert_id;
    $stmt->close();

    $stmtDetails = $db->prepare("INSERT INTO user_details (userId, mothersMaidenName, country, city, street, buildingNumber, apartmentNumber, postalCode) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtDetails->bind_param("isssssss", $userId, $mName, $country, $city, $street, $bdNum, $apNum, $postal);
    if (!$stmtDetails->execute()) {
        throw new Exception("Error adding user details: " . $stmtDetails->error);
    }
    $stmtDetails->close();

    $balance = 0;
    $stmtAccount = $db->prepare("INSERT INTO accounts (userId, balance, createdAt) VALUES (?, ?, ?)");
    $stmtAccount->bind_param("iis", $userId, $balance, $createdAt);
    if (!$stmtAccount->execute()) {
        throw new Exception("Error adding account: " . $stmtAccount->error);
    }
    $stmtAccount->close();

    $result = $db->query("SELECT cardNumber FROM cards ORDER BY cardId DESC LIMIT 1");
    $lastCardNumber = $result->fetch_assoc()['card_number'] ?? '7000000000';
    $cardNumber = (int)$lastCardNumber + 1;
    while (substr((string)$cardNumber, 0, 1) !== '7') {
        $cardNumber++;
    }

    $expirationDate = date('Y-m-d', strtotime('+5 years'));
    $cardholderName = $name . " " . $surname;

    $stmtCard = $db->prepare("INSERT INTO cards (userId, cardNumber, expirationDate, cardholderName, status, createdAt) VALUES (?, ?, ?, ?, ?, ?)");
    $status = 'active';
    $stmtCard->bind_param("iissss", $userId, $cardNumber, $expirationDate, $cardholderName, $status, $createdAt);
    if (!$stmtCard->execute()) {
        throw new Exception("Error adding card: " . $stmtCard->error);
    }
    $stmtCard->close();

    $db->commit();

    echo "User, details, account, and card created successfully!";
    header("Location: ../../../login.html");

    if (isset($db)) {
        $db->close();
    }

?>
