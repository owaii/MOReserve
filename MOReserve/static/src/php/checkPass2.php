<?php
include "conn.php";

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$input = json_decode(file_get_contents('php://input'), true);

$id = $input["id"];
$password = $input["password"];

$stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $id); 

$stmt->execute();

$stmt->bind_result($storedPasswordHash);

if ($stmt->fetch()) {
    if (password_verify($password, $storedPasswordHash)) {
        echo json_encode(["success" => true, "mess" => "Your password is correct"]);
        exit(); 
    } else {
        echo json_encode(["success" => false, "mess" => "Your password isnt correct"]);
    }
} else {
    echo json_encode(["success" => true, "mess" => "We couldnt fetch the result"]);
}

$stmt->close();
$db->close();

