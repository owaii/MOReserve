<?php
$db = new mysqli('localhost', 'root', '', 'more');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$username = $_GET["username"];
$password = $_GET["password"];

$stmt = $db->prepare("SELECT username, password, id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);  // Bind the username parameter to the query

$stmt->execute();

$stmt->bind_result($storedUsername, $storedPasswordHash, $id);

if ($stmt->fetch()) {
    if (password_verify($password, $storedPasswordHash)) {
        header("Location: ../../../dashboard.php?page=dashboard&id=".$id);
        exit(); 
    } else {
        header("Location: ../../../login.html");
    }
} else {
    header("Location: ../../../login.html");
}

$stmt->close();
$db->close();

