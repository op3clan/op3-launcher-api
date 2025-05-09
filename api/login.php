<?php
// Enable CORS and plain text response
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/plain");

// Show errors for debugging (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Error: Invalid request method";
    exit;
}

// Get POST values
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo "Error: Missing username or password";
    exit;
}

// DB connection
$conn = new mysqli("sql201.infinityfree.com", "if0_38927845", "Underhill903903", "if0_38927845_op3clan");

// Connection error check
if ($conn->connect_error) {
    echo "Error: DB connection failed - " . $conn->connect_error;
    exit;
}

// Prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT password_hash FROM users WHERE username = ?");
if (!$stmt) {
    echo "Error: Prepare failed - " . $conn->error;
    exit;
}

$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    $stmt->bind_result($password_hash);
    $stmt->fetch();

    if (password_verify($password, $password_hash)) {
        echo "success";
    } else {
        echo "Error: Invalid password";
    }
} else {
    echo "Error: Username not found";
}

$stmt->close();
$conn->close();
?>
