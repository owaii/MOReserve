<?php
include "conn.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file']) && isset($_POST['user_id'])) {
    $file = $_FILES['file'];
    $userId = $_POST['user_id'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'File upload error']);
        exit;
    }

    $uploadDir = '../../img/users/pfp/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = uniqid() . '-' . basename($file['name']);
    $uploadPath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        try {
            $stmt = $db->prepare("UPDATE users SET `icon` = '$fileName' WHERE `id` = '$userId'");
            $stmt->execute();

            echo json_encode(['success' => true, 'filePath' => $fileName]);

        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'File could not be uploaded']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No file or user_id provided']);
}
?>
