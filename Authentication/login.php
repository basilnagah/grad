<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../conn/connection.php';
require_once '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod == "POST") {
    $inputData = json_decode(file_get_contents("php://input"), true);

    if (empty($inputData)) {
        $storeCustomer = loginToAccount($_POST);
    } else {
        $storeCustomer = loginToAccount($inputData);
    }

    echo $storeCustomer;
} else {
    $data = [
        "status" => 405,
        "message" => "$requestMethod method not allowed",
    ];
    header("HTTP/1.0 405 Method not allowed");
    echo json_encode($data);
}

function loginToAccount($customerInput) {
    global $conn;

    // Check if connection is valid
    if (!$conn || $conn->connect_error) {
        return json_encode([
            "status" => 500,
            "message" => "Database connection failed"
        ]);
    }

    $email = mysqli_real_escape_string($conn, $customerInput['email']);
    $password = mysqli_real_escape_string($conn, $customerInput['password']);

    if (empty(trim($email))) {
        return error422('email is required');
    } else if (empty(trim($password))) {
        return error422('password is required');
    }

    $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user['password'])) {
                $payload = [
                    "iss" => "https://localhost/EMAIL", // Issuer
                    "iat" => time(), // Issued at
                    "exp" => time() + 60, // Expiration (1 hour)
                    "user_id" => $user['id'],
                    "email" => $user['email'],
                    "role" => $user['role'],
                ];

                $secret_key = "your_secret_key_here"; // Replace with a secure key
                $jwt = JWT::encode($payload, $secret_key, 'HS256');

                return json_encode([
                    "status" => 200,
                    "message" => "login successful",
                    "user" => [
                        "id" => $user['id'],
                        "name" => $user['name'],
                        "email" => $user['email'],
                        "role" => $user['role'],
                    ],
                    "token" => $jwt
                ]);
            } else {
                return json_encode(["status" => 401, "message" => "Invalid password"]);
            }
        } else {
            return json_encode(["status" => 404, "message" => "User not found"]);
        }
    } else {
        return json_encode(["status" => 500, "message" => "Query failed: " . mysqli_error($conn)]);
    }
}

function error422($message) {
    $data = [
        "status" => 422,
        "message" => $message,
    ];
    header("HTTP/1.0 422 unprocessable entity");
    return json_encode($data);
}
?>
