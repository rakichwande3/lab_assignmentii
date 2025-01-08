<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['temp_user'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['temp_user']['id'];
$email = $_SESSION['temp_user']['email'];

// Generate a new OTP and expiry time
$new_otp = rand(100000, 999999);
$otp_expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

// Update the OTP in the database
$sql = "UPDATE users SET otp = :otp, otp_expiry = :otp_expiry WHERE id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':otp', $new_otp, PDO::PARAM_STR);
$stmt->bindParam(':otp_expiry', $otp_expiry, PDO::PARAM_STR);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

if ($stmt->execute()) {
    // Send the new OTP via email
    $subject = "Your New OTP Code";
    $message = "Hello,\n\nYour new OTP code is: $new_otp\n\nThis code will expire in 10 minutes.\n\nRegards,\nYour Company";
    $headers = "From: no-reply@yourdomain.com";

    if (mail($email, $subject, $message, $headers)) {
        echo "<script>
                alert('A new OTP has been sent to your email.');
                window.location.href = 'otp_verification.php';
              </script>";
    } else {
        echo "<script>
                alert('Failed to send OTP. Please try again.');
                window.location.href = 'otp_verification.php';
              </script>";
    }
} else {
    echo "<script>
            alert('Failed to generate a new OTP. Please try again.');
            window.location.href = 'otp_verification.php';
          </script>";
}
?>
