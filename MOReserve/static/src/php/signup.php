<?php
$db = new mysqli('localhost', 'root', '', 'more');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

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

$hashedPass = password_hash($password, PASSWORD_DEFAULT);

$db->begin_transaction();

try {
    $stmtPersonal = $db->prepare("
        INSERT INTO personal (mothersMaidenName, country, city, street, buildingNumber, apartmentNumber, postal)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmtPersonal->bind_param("sssssss", $mName, $country, $city, $street, $bdNum, $apNum, $postal);
    $stmtPersonal->execute();

    $personalID = $db->insert_id;
    
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

    
    $stmtCardNumber = $db->prepare("SELECT number FROM cards");
    $stmtCardNumber->execute();
    $result = $stmtCardNumber->get_result();

    $number = random_int(7000000000000000, 7999999999999999);

    while ($result->num_rows > 0) {
        $matchFound = false;

        while ($row = $result->fetch_assoc()) {
            if ($number == $row["number"]) {
                $matchFound = true;
                break;
            }
        }

        if ($matchFound) {
            $number++;
        } else {
            break;
        }

        $result->data_seek(0);
    }


    $stmtCard = $db->prepare("
        INSERT INTO cards (userID, number, date, holderName, cvv, status)
        VALUES (?, ?, ?, ?, ?,'active')
    ");
    $stmtCard->bind_param("iissi", $userID, $number, $date, $fullName, $cvv);
    $stmtCard->execute();

    $db->commit();

    echo json_encode(["success" => true, "message" => "User created successfully."]);
} catch (Exception $e) {
    $db->rollback();
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
} finally {
    $stmtUsers->close();
    $stmtPersonal->close();
    $stmtCard->close();
    $db->close();
}
