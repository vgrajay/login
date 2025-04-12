<?php
// Database configuration
$servername = "localhost"; // Change if using a different host
$username = "root"; // Change to your database username
$password = ""; // Change to your database password
$dbname = "user_auth"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get email from POST request
$email = $_POST['email'];

// Check if the email exists in the users table
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Email already exists
    echo "exists";
} else {
    // Email does not exist
    echo "available";
}

$stmt->close();
$conn->close();
?>
