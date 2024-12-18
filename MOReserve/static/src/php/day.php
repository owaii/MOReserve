<?php
include "conn.php";

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

if ($id <= 0) {
    echo json_encode(["status" => false, "message" => "Invalid user ID"]);
    exit;
}

$stmt = $db->prepare("
    SELECT 
        CASE 
            WHEN HOUR(time) >= 0 AND HOUR(time) < 4 THEN '1'
            WHEN HOUR(time) >= 4 AND HOUR(time) < 8 THEN '2'
            WHEN HOUR(time) >= 8 AND HOUR(time) < 12 THEN '3'
            WHEN HOUR(time) >= 12 AND HOUR(time) < 16 THEN '4'
            WHEN HOUR(time) >= 16 AND HOUR(time) < 20 THEN '5'
            WHEN HOUR(time) >= 20 AND HOUR(time) < 24 THEN '6'
        END AS time_range,
        ROUND(AVG(amount), 0) AS avg_amount
    FROM 
        transactions
    WHERE 
        DATE(created) = CURRENT_DATE
        AND userID = ?
    GROUP BY 
        time_range;
");

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

$number = [];
$value = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        array_push($number, $row["time_range"]);
        array_push($value, $row["avg_amount"]);
    }
} else {
    echo json_encode(["status" => false, "message" => "No data found for the current week"]);
    exit;
}

echo json_encode(["status" => true, "number" => $number, "val" => $value]);

$stmt->close();
?>
