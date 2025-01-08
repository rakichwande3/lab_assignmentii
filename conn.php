<?php 
$host = "localhost";     // Hostname 
$port = "5432";          // Default PostgreSQL port
$db = "lab_assignmentii"; // Database name
$user = "postgres";      // Database username
$pass = "rakich"; // Database password

try {
    // Create a new PDO instance
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    
    // Set error mode to exception for debugging
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Uncomment for testing
    // echo "Connected successfully";
} catch (PDOException $e) {
    // Handle connection errors
    // It's a good practice not to echo the full error in production
    die("Connection failed: " . $e->getMessage());
}
?>
