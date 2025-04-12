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

// Get form data
$email = $_POST['email'];
$password = $_POST['password'];
$otp = $_POST['otp']; // ID used for verification

// Check if the provided otp and email exist in the otp_table
$sql = "SELECT * FROM otp_table WHERE email = ? AND otp = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $otp);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Invalid ID number or email does not match with ID!"; // ID and email do not match
} else {
    // Check if the email already exists in the users table
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Redirect to the alreadyexist.html page if email already exists
        header("Location: index.html");
        exit(); // Make sure to call exit after the header to stop further execution
    } else {
        // ID and email match, hash the password before storing it
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert the hashed password into the users table
        $sql = "INSERT INTO users (email, password) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $hashed_password);
        
        if ($stmt->execute()) {
            $_SESSION['user_email'] = $email;
    header("Location: dashboard.html");
            echo "Error: " . $stmt->error;
        }
    }
}

$stmt->close();
$conn->close();
?>
