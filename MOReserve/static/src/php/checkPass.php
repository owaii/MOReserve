<?php
// Create a connection to the database
$db = new mysqli('localhost', 'root', '', 'more');

// Check if the connection was successful
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get the username and password from the URL parameters (e.g., ?username=user&password=pass)
$username = $_GET["username"];
$password = $_GET["password"];  // Assuming the user is submitting a password as well

// Prepare a statement to avoid SQL injection
$stmt = $db->prepare("SELECT username, password, id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);  // Bind the username parameter to the query

// Execute the query
$stmt->execute();

// Bind the result variables
$stmt->bind_result($storedUsername, $storedPasswordHash, $id);

// Check if a user was found
if ($stmt->fetch()) {
    // If the username is found, verify the password
    if (password_verify($password, $storedPasswordHash)) {
        // Password is correct, redirect to dashboard
        header("Location: ../../../dashboard.php?page=dashboard&id=".$id);
        exit();  // Make sure to call exit() after the header to stop further script execution
    } else {
        // Password is incorrect
        header("Location: ../../../login.html");
    }
} else {
    // No user found with that username
    header("Location: ../../../login.html");
}

// Close the statement and database connection
$stmt->close();
$db->close();

