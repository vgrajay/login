<?php
session_start();
include("db.php"); // Your DB connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $enteredOtp = $_POST['otp'];
  $newPassword = $_POST['new_password'];
  $confirmPassword = $_POST['confirm_password'];

  // Check if passwords match
  if ($newPassword !== $confirmPassword) {
    echo "Passwords do not match.";
    exit;
  }

  // Hash the new password
  $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

  // Fetch OTP from DB
  $stmt = $conn->prepare("SELECT otp FROM otp_table WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $correctOtp = $row['otp'];

    if ($enteredOtp == $correctOtp) {
      // OTP verified - update password
      $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
      $update->bind_param("ss", $hashedPassword, $email);
      if ($update->execute()) {
        $_SESSION['user_email'] = $email;
    header("Location: dashboard.html");
        // Optional: delete used OTP
        $deleteOtp = $conn->prepare("DELETE FROM otp_table WHERE email = ?");
        $deleteOtp->bind_param("s", $email);
        $deleteOtp->execute();
      } else {
        echo "Error updating password.";
      }
    } else {
      echo "Invalid OTP.";
    }
  } else {
    echo "No OTP found for this email.";
  }
}
?>
