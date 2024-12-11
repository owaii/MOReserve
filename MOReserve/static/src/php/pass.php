<?php
// Database connection
$db = new mysqli("localhost", "root", "", "more");

// Check for connection errors
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get the form data (new password, old password, and user ID)
$oldPassword = $_POST["oldVal"];
$newPassword = $_POST["newVal"];
$id = $_GET["id"];

// Prepare a statement to retrieve the current hashed password from the database
$stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Check if the user exists
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hashedPassword = $row["password"];

    // Verify the old password with the hashed password
    if (password_verify($oldPassword, $hashedPassword)) {
        // If the old password is correct, hash the new password and update it
        $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the password in the database
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $newHashedPassword, $id);
        
        if ($stmt->execute()) {
            // Redirect to settings page after successful password update
            header("Location: ../../../dashboard.php?page=settings&id=" . $id);
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        die("Error: Current password is incorrect.");
    }
} else {
    echo "Error: User not found.";
}

// Close the database connection
$db->close();
?>
