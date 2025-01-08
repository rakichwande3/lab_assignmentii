<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['temp_user'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_otp = $_POST['otp'];
    $stored_otp = $_SESSION['temp_user']['otp'];
    $user_id = $_SESSION['temp_user']['id'];

    // Prepare the SQL query to verify OTP
    $sql = "SELECT * FROM users WHERE id = :user_id AND otp = :user_otp";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_otp', $user_otp, PDO::PARAM_STR);

    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        // Check if OTP is not expired
        $otp_expiry = strtotime($data['otp_expiry']);
        if ($otp_expiry >= time()) {
            // OTP is valid and not expired
            $_SESSION['user_id'] = $data['id'];
            unset($_SESSION['temp_user']); // Clear temporary session data
            header("Location: dashboard.php"); // Redirect to dashboard
            exit();
        } else {
            // OTP has expired
            $error_message = "OTP has expired. Please request a new one.";
        }
    } else {
        // Invalid OTP entered
        $error_message = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OTP Verification</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>OTP Verification</h3>
                    </div>
                    <div class="card-body">
                        <!-- Display error message if any -->
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger">
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="otp_verification.php">
                            <div class="mb-3">
                                <label for="otp" class="form-label">Enter OTP Code</label>
                                <input type="number" name="otp" id="otp" class="form-control" placeholder="Six-Digit OTP" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Verify OTP</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <p><a href="resend_otp.php" class="text-decoration-none">Resend OTP</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
