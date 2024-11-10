<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

$expense_id = $_POST['expense_id'];
$user_id = $_SESSION['user_id']; // Retrieve user ID from session
$new_amount = $_POST['amount'];
$reason = $_POST['reason'];

// Fetch previous amount before updating
$query = "SELECT amount FROM expenses WHERE id = ?";
$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("i", $expense_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $previous_amount = $result->fetch_assoc()['amount'];
    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error;
    exit();
}

// Insert the edit request into the edit_requests table
$stmt = $conn->prepare("INSERT INTO edit_requests (expense_id, user_id, request_status, new_amount, reason) VALUES (?, ?, 'Pending', ?, ?)");
if ($stmt) {
    $stmt->bind_param("iiss", $expense_id, $user_id, $new_amount, $reason);
    if ($stmt->execute()) {
        // Log the changes in the expense_edit_logs table
        $log_query = "INSERT INTO expense_edit_logs (expense_id, user_id, previous_amount, new_amount) VALUES (?, ?, ?, ?)";
        $log_stmt = $conn->prepare($log_query);
        if ($log_stmt) {
            $log_stmt->bind_param("iidd", $expense_id, $user_id, $previous_amount, $new_amount);
            $log_stmt->execute();
            $log_stmt->close();
        } else {
            echo "Error preparing log statement: " . $conn->error;
        }

        // Redirect back to the index page
        header('Location: index.php');
        exit();
    } else {
        echo "Error inserting request: " . $stmt->error;
    }
} else {
    echo "Error preparing statement: " . $conn->error;
}
?>
