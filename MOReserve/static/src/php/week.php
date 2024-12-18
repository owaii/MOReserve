<?php
include "conn.php";

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

if ($id <= 0) {
    echo json_encode(["status" => false, "message" => "Invalid user ID"]);
    exit;
}

$stmt = $db->prepare("
    SELECT 
        DAYOFWEEK(created) AS day_of_week,  
        ROUND(AVG(amount), 0) AS avg_amount
    FROM 
        transactions
    WHERE 
        YEARWEEK(created, 1) = YEARWEEK(CURDATE(), 1)
        AND userID = ?
    GROUP BY 
        DAYOFWEEK(created)
    ORDER BY 
        day_of_week;
");

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

$day = [];
$res = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        array_push($day, $row["day_of_week"]);
        array_push($res, $row["avg_amount"]);
    }
} else {
    echo json_encode(["status" => false, "message" => "No data found for the current week"]);
    exit;
}

echo json_encode(["status" => true, "day" => $day, "avg" => $res]);

$stmt->close();
?>
