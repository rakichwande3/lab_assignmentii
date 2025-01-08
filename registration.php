<?php
session_start();
include 'conn.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Basic email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script> alert('Invalid email format.');</script>";
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Check if the email already exists using a prepared statement
            $checkEmailSql = "SELECT * FROM users WHERE email = :email";
            $stmt = $conn->prepare($checkEmailSql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            // Check if the email is already registered
            if ($stmt->rowCount() > 0) {
                echo "<script> alert('Email is already registered. Please use a different email.');</script>";
            } else {
                // Insert the new user into the database
                $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    echo "<script>
                            alert('Registration Successful.');
                            function navigateToPage() {
                                window.location.href = 'index.php';
                            }
                            window.onload = function() {
                                navigateToPage();
                            }
                        </script>";
                } else {
                    echo "<script> alert('Registration Failed. Please try again.');</script>";
                }
            }
        } catch (PDOException $e) {
            echo "<script> alert('Error: " . $e->getMessage() . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: #fff;
        }
        #container {
            background: #fff;
            color: #000;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
        }
        #container form {
            margin: 0 auto;
        }
        input[type=text], input[type=password] {
            border-radius: 5px;
        }
        input[type=submit] {
            background-color: #6a11cb;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        input[type=submit]:hover {
            background-color: #2575fc;
            cursor: pointer;
        }
        a {
            color: #6a11cb;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        @media (max-width: 576px) {
            #container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div id="container">
        <h2 class="text-center mb-4">Register</h2>
        <form method="post" action="registration.php">
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" name="username" class="form-control" placeholder="Enter Username" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="text" name="email" class="form-control" placeholder="Enter Your Email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" name="password" class="form-control" placeholder="Enter Password" required>
            </div>
            <div class="d-grid">
                <input type="submit" name="register" value="Register" class="btn btn-primary">
            </div>
            <div class="text-center mt-3">
                <label>Already have an account? </label>
                <a href="index.php">Login</a>
            </div>
        </form>
    </div>
    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

