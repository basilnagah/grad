<?php
// Retrieve database credentials from Railway environment variables
$servername = getenv("MYSQLHOST") ?: "shinkansen.proxy.rlwy.net"; // Fallback to localhost for local dev
$username = getenv("MYSQLUSER") ?: "root";
$password = getenv("MYSQLPASSWORD") ?: "vtEYOwzOZYCNwFqkaEqfEowwYWKKlmUr";
$dbname = getenv("MYSQLDATABASE") ?: "railway";
$port = getenv("MYSQLPORT") ?: 3306;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    // Log the error for debugging (don't expose in production)
    error_log("Connection failed: " . $conn->connect_error);
    // Return a proper JSON response instead of dying
    header("Content-Type: application/json");
    echo json_encode([
        "status" => 500,
        "message" => "Database connection failed"
    ]);
    exit();
}
?>
