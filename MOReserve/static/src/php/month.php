<?php include "conn.php";

	$id = $_GET["id"];

	$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

	if ($id <= 0) {
	    echo json_encode(["status" => false, "message" => "Invalid user ID"]);
	    exit;
	}

	$stmt = $db->prepare("SELECT amount, DAY(created) as day FROM transactions WHERE MONTH(created) = MONTH(CURRENT_DATE) AND YEAR(created) = YEAR(CURRENT_DATE) AND userID = ?;");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();

	$day = [];
	$amount = [];

	if ($result->num_rows > 0) {
	    while ($row = $result->fetch_assoc()) {
	        array_push($day, $row["day"]);
	        array_push($amount, $row["amount"]);
	    }
	} else {
	    echo json_encode(["status" => false, "message" => "No data found for the current week"]);
	    exit;
	}

	$week1 = 0;
	$week2 = 0;
	$week3 = 0;
	$week4 = 0;

	for ($i = 0; $i < count($day); $i++) {
		$val = (int)$day[$i];
		if ($val > 0 && $val <= 7) {
			$week1 += (int)$amount[$i];
		} else if ($val > 7 && $val <= 14) {
			$week2 += (int)$amount[$i];
		} else if ($val > 14 && $val <= 21) {
			$week3 += (int)$amount[$i];
		} else {
			$week4 += (int)$amount[$i];
		}
	}

	echo json_encode(["status" => true, "week1" => $week1, "week2" => $week2, "week3" => $week3, "week4" => $week4]);

	$stmt->close();

