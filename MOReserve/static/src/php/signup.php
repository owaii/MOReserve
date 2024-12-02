<?php
// Create a connection to the database
$db = new mysqli('localhost', 'root', '', 'more');

// Check if the connection was successful
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Retrieve values from the URL using $_GET (check if they are set to avoid undefined index)
$username = isset($_GET['username']) ? $_GET['username'] : '';
$email = isset($_GET['email']) ? $_GET['email'] : '';
$password = isset($_GET['pass']) ? $_GET['pass'] : '';
$phoneNum = isset($_GET['phoneNum']) ? $_GET['phoneNum'] : '';
$pesel = isset($_GET['pesel']) ? $_GET['pesel'] : '';
$mName = isset($_GET['mName']) ? $_GET['mName'] : '';
$country = isset($_GET['country']) ? $_GET['country'] : '';
$city = isset($_GET['city']) ? $_GET['city'] : '';
$street = isset($_GET['street']) ? $_GET['street'] : '';
$bdNum = isset($_GET['bdNum']) ? $_GET['bdNum'] : '';
$apNum = isset($_GET['apNum']) ? $_GET['apNum'] : '';
$postal = isset($_GET['postal']) ? $_GET['postal'] : '';

// Hash the password
$hashedpass = password_hash($password, PASSWORD_DEFAULT);
$createdAt = date('Y-m-d');
$lastLogin = $createdAt;

// Prepare an SQL statement to insert into the users table
$stmt = $db->prepare("
    INSERT INTO users (username, email, password_hash, phone_number, created_at, last_login)
    VALUES (?, ?, ?, ?, ?, ?)
");

// Check if the preparation was successful
if ($stmt === false) {
    die("Failed to prepare query: " . $db->error);
}

// Bind parameters to the SQL query (all values are strings except for the dates)
$stmt->bind_param(
    "ssssss", // 's' denotes string type for each parameter
    $username, $email, $hashedpass, $phoneNum, $createdAt, $lastLogin
);

// Execute the query
if ($stmt->execute()) {
    echo "User created successfully!<br>";

    // Get the inserted user's ID
    $user_id = $db->insert_id;

    // Prepare an SQL statement to insert into the user_details table
    $stmt_details = $db->prepare("
        INSERT INTO user_details (user_id, pesel, mothers_maiden_name, country, city, street, building_number, apartment_number, postal_code)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    // Bind parameters for the second statement
    $stmt_details->bind_param(
        "issssssss", // 'i' for integer and 's' for strings
        $user_id, $pesel, $mName, $country, $city, $street, $bdNum, $apNum, $postal
    );

    // Execute the second query to insert user details
    if ($stmt_details->execute()) {
        echo "User details added successfully!";
    } else {
        echo "Error adding user details: " . $stmt_details->error;
    }

    // Close the user_details statement
    $stmt_details->close();

} else {
    echo "Error adding user: " . $stmt->error;
}

// Close the main statement and database connection
$stmt->close();
$db->close();

header("../../../login.html");
