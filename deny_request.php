<?php
include 'db.php';

$request_id = $_GET['id'];

// Mark the edit request as denied
$stmt = $conn->prepare("UPDATE edit_requests SET request_status = 'Denied' WHERE id = ?");
$stmt->bind_param("i", $request_id);
$stmt->execute();

header('Location: admin_requests.php');
?>
