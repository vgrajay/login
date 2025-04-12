<?php
$conn = new mysqli("localhost", "root", "", "your_db_name");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

  // Check if user exists
  $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $check->bind_param("s", $email);
  $check->execute();
  $result = $check->get_result();

  if ($result->num_rows > 0) {
    // Update password
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $new_password, $email);
    $stmt->execute();
    echo "Password reset successful. <a href='index.html'>Login now</a>";
  } else {
    echo "No user found with this email.";
  }
}
?>
