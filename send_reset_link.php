<?php
session_start();
include 'conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if the email exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Generate unique code
        $reset_code = bin2hex(random_bytes(16)); 
        $expiry_time = date("Y-m-d H:i:s", strtotime("+15 minutes"));

        // Update reset_code and expiry in the database
        $stmt = $conn->prepare("UPDATE users SET reset_code = :reset_code, reset_expiry = :expiry_time WHERE email = :email");
        $stmt->bindParam(':reset_code', $reset_code, PDO::PARAM_STR);
        $stmt->bindParam(':expiry_time', $expiry_time, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        // Send email (simplified)
        $reset_link = "http://yourdomain.com/reset_password.php?code=$reset_code";
        $subject = "Password Reset Request";
        $message = "Click the link below to reset your password:\n\n$reset_link";
        mail($email, $subject, $message, "From: no-reply@yourdomain.com");

        echo "A reset link has been sent to your email address.";
    } else {
        echo "Email not found.";
    }
}
?>
