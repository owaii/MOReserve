<?php 
    $db = new mysqli("localhost", "root", "", "more");

    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }
    
    $input = json_decode(file_get_contents("php://input"), true);

    $id = intval($input['id'] ?? 0);

    if ($id === 0) {
        echo json_encode(["success" => false, "message" => "Invalid user ID"]);
        exit;
    }

    $db->begin_transaction();

    try {
        $stmt = $db->prepare("DELETE FROM friends WHERE userID = ? OR friendID = ?");
        $stmt->bind_param("ii", $id, $id);
        $stmt->execute();

        $stmt = $db->prepare("DELETE FROM transactions WHERE userID = ? OR toUserID = ?");
        $stmt->bind_param("ii", $id, $id);
        $stmt->execute();

        $stmt = $db->prepare("DELETE FROM cards WHERE userID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $stmt = $db->prepare("DELETE FROM personal WHERE id NOT IN (SELECT personalID FROM users)");
        $stmt->execute();

        $db->commit();

        echo json_encode(["success" => true, "message" => "User deleted successfully"]);
    } catch (Exception $e) {
        $db->rollback();

        echo json_encode(["success" => false, "message" => "Error deleting user: " . $e->getMessage()]);
    } finally {
        $db->close();
    }

    exit;
?>
