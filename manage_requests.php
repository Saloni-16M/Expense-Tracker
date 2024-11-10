<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Check if a status change request was made
if (isset($_GET['id']) && isset($_GET['status'])) {
    $request_id = $_GET['id'];
    $status = $_GET['status'];

    // Fetch the new amount from the request
    $stmt = $conn->prepare("SELECT new_amount, expense_id FROM edit_requests WHERE id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $request = $result->fetch_assoc();

    if ($status == 'Approved') {
        // Update the expenses table with the new amount
        $stmt = $conn->prepare("UPDATE expenses SET amount = ? WHERE id = ?");
        $stmt->bind_param("di", $request['new_amount'], $request['expense_id']);
        if (!$stmt->execute()) {
            echo "Error updating expense: " . $stmt->error;
            exit();
        }

        // Debugging: Print out values for verification
        echo "Expense ID: " . $request['expense_id'] . "<br>";
        echo "User ID: " . $_SESSION['user_id'] . "<br>";
        echo "Previous Amount: " . $previous_amount . "<br>";
        echo "New Amount: " . $request['new_amount'] . "<br>";

        // Update the log entry with the acceptance timestamp
         // Update the log entry with the acceptance timestamp
         $log_update_query = "UPDATE expense_edit_logs SET acceptance_timestamp = CURRENT_TIMESTAMP WHERE expense_id = ? AND user_id = ? AND previous_amount = ? AND new_amount = ?";
         $log_update_stmt = $conn->prepare($log_update_query);
         if ($log_update_stmt) {
             $log_update_stmt->bind_param("iidd", $request['expense_id'], $_SESSION['user_id'], $previous_amount, $request['new_amount']);
             if (!$log_update_stmt->execute()) {
                 echo "Error updating log: " . $log_update_stmt->error;
                 exit();
             }
             $log_update_stmt->close();
         } else {
             echo "Error preparing log update statement: " . $conn->error;
             exit();
         }
     }
    // Update the request status
    $stmt = $conn->prepare("UPDATE edit_requests SET request_status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $request_id);
    if (!$stmt->execute()) {
        echo "Error updating request status: " . $stmt->error;
        exit();
    }

    header('Location: manage_requests.php'); // Redirect to refresh the page
    exit();
}

// Fetch pending requests
$result = $conn->query("SELECT er.id, i.item_name, er.request_status, u.username, er.new_amount, er.reason 
                        FROM edit_requests er 
                        JOIN expenses e ON er.expense_id = e.id 
                        JOIN items i ON e.item_id = i.id 
                        JOIN users u ON er.user_id = u.id 
                        WHERE er.request_status = 'Pending'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1 class="mt-5">Manage Edit Requests</h1>

    <table class="table table-striped">
        <thead>
        <tr>
            <th>Item Name</th>
            <th>Username</th>
            <th>New Amount</th>
            <th>Reason</th>
            <th>Current Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['new_amount']); ?></td>
                <td><?php echo htmlspecialchars($row['reason']); ?></td>
                <td><?php echo htmlspecialchars($row['request_status']); ?></td>
                <td>
                    <a href="manage_requests.php?id=<?php echo $row['id']; ?>&status=Approved" class="btn btn-success btn-sm">Approve</a>
                    <a href="manage_requests.php?id=<?php echo $row['id']; ?>&status=Denied" class="btn btn-danger btn-sm">Deny</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
