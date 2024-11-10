<?php
include 'db.php';

$request_id = $_GET['id'];
$status = $_GET['status'];

// Get the request details
$request = $conn->query("SELECT * FROM edit_requests WHERE id = $request_id")->fetch_assoc();
$expense_id = $request['expense_id'];

if ($status == 'approved') {
    // Update the expense with the new amount
    $new_amount = $request['new_amount'];
    $conn->query("UPDATE expenses SET amount = $new_amount WHERE id = $expense_id");
}

// Update the request status
$stmt = $conn->prepare("UPDATE edit_requests SET request_status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $request_id);
$stmt->execute();

header('Location: manage_requests.php');
?>
