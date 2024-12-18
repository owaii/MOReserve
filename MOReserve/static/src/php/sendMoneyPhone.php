<?php
$db = new mysqli("localhost", "root", "", "more");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$input = json_decode(file_get_contents("php://input"), true);
$id = intval($input['id'] ?? 0);
$value = floatval($input['value'] ?? 0);
$phone = intval($input['phone'] ?? 0);
$userID = intval($input['userID'] ?? 0);
$desc = intval($input['desc'] ?? "Money Transfer");

$db->begin_transaction();

try {
    $stmt = $db->prepare("SELECT id FROM users WHERE phoneNumber = ?");
    $stmt->bind_param("i", $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userID = $row["id"];
    } else {
        throw new Exception("User to send to not found");
    }
    $stmt->close();

    $stmt = $db->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $balance = floatval($row["balance"]);

        if ($balance >= $value) {
            $newBalance = $balance - $value;
            $stmt = $db->prepare("UPDATE users SET balance = ? WHERE id = ?");
            $stmt->bind_param("di", $newBalance, $id);
            $stmt->execute();
            $stmt->close();

            $stmt = $db->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
            $stmt->bind_param("di", $value, $userID);
            $stmt->execute();
            $stmt->close();

            $stmt = $db->prepare("INSERT INTO transactions (userID, toUserId, amount, description) VALUES (?, ?, ?, ?)");
            $description = "Money transfer";
            $stmt->bind_param("iiis", $id, $userID, $value, $description);
            $stmt->execute();
            $stmt->close();

            $db->commit();
            echo json_encode(["success" => true, "message" => "Transaction successful."]);
        } else {
            throw new Exception("Insufficient balance.");
        }
    } else {
        throw new Exception("User not found.");
    }
} catch (Exception $e) {
    $db->rollback();
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}

$db->close();
?>
