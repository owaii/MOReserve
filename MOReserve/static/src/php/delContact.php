<?php
include "conn.php";

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$input = json_decode(file_get_contents('php://input'), true);

$id = $input["id"];
$friendID = $input["friendID"];

$stmt = $db->prepare("DELETE FROM friends WHERE `friendID` = ? AND `userID` = ?");
$stmt->bind_param("ii", $friendID, $id); 

if($stmt->execute()) {
    echo json_encode(["success" => true, "mess" => "You deleted a preson correctly"]);
} else {
    echo json_encode(["success" => false, "mess" => "jgfhnsjdgh"]);
}exit();


