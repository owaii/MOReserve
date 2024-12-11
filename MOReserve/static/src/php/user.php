<?php 
	// Establishing the database connection
	$db = new mysqli("localhost", "root", "", "more");

	// Checking if the connection was successful
	if ($db->connect_error) {
		die("Connection failed: " . $db->connect_error);
	}

	// Getting the new username and the user ID from the URL parameters
	$username = $_GET["newVal"];
	$id = $_GET["id"];

	// Prepare the SQL query to update the username
	$stmt = $db->prepare("UPDATE users SET username = ? WHERE id = ?;");
	$stmt->bind_param("si", $username, $id);

	// Executing the query
	if ($stmt->execute()) {
		// Redirecting to the settings page after a successful update
		header("Location: ../../../dashboard.php?page=settings&id=".$id);
		exit();  // Ensure the script stops execution after the redirect
	} else {
		// Handle any errors during the execution
		echo "Error: " . $stmt->error;
	}

	// Closing the statement and connection
	$stmt->close();
	$db->close();
?>
