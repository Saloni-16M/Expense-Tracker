<?php
session_start();
include 'db.php';

$username = $_POST['username'];
$password = $_POST['password'];

// Prepare and execute query to check credentials
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND password = ?");
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Login successful
    $user = $result->fetch_assoc();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $username;
    header('Location: index.php'); // Redirect to admin page
    exit();
} else {
    // Login failed
    header('Location: login.php?error=1'); // Redirect back to login with error
    exit();
}
?>
