<?php
include 'db.php';

$expense_id = $_POST['delete_id'];

$conn->query("DELETE FROM expenses WHERE id = $expense_id");

header('Location: index.php');
?>

