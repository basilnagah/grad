<?php
require_once '../conn/connection.php';

if (!$conn || $conn->connect_error) {
    echo "Connection failed: " . $conn->connect_error;
} else {
    echo "Connected successfully!";
}
?>
