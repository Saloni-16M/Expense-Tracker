<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['amount'] as $item_id => $amount) {
        if (!empty($amount)) {
            $date = date('Y-m-d H:i:s');
            $sql = "INSERT INTO expenses (item_id, amount, date) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ids", $item_id, $amount, $date);
            $stmt->execute();
        }
    }
}

header('Location: index.php');
exit();
?>
