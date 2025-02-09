<?php
include 'conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reset_code = $_POST['code'];
    $new_password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if reset_code is valid
    $stmt = $conn->prepare("SELECT * FROM users WHERE reset_code = :reset_code");
    $stmt->bindParam(':reset_code', $reset_code, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Verify code expiry
        if (strtotime($user['reset_expiry']) >= time()) {
            // Update password
            $stmt = $conn->prepare("UPDATE users SET password = :password, reset_code = NULL, reset_expiry = NULL WHERE reset_code = :reset_code");
            $stmt->bindParam(':password', $new_password, PDO::PARAM_STR);
            $stmt->bindParam(':reset_code', $reset_code, PDO::PARAM_STR);
            $stmt->execute();

            echo "Password successfully reset. <a href='index.php'>Login</a>";
        } else {
            echo "Reset code has expired.";
        }
    } else {
        echo "Invalid reset code.";
    }
}
?>
