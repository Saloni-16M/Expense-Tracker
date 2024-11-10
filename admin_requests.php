<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Edit Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1 class="mt-5">Pending Edit Requests</h1>

    <?php
    include 'db.php';

    // Fetch pending edit requests
    $result = $conn->query("SELECT er.id as request_id, er.expense_id, er.new_amount, er.request_date,er.reason, i.item_name, e.amount as old_amount 
                            FROM edit_requests er
                            JOIN expenses e ON er.expense_id = e.id
                            JOIN items i ON e.item_id = i.id
                            WHERE er.status = 'Pending'
                            ORDER BY er.request_date DESC");

    if ($result->num_rows > 0) {
        echo "<table class='table table-striped'>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Old Amount</th>
                    <th>New Amount</th>
                    <th>Request Date</th>
                    <th>Actions</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['item_name']}</td>
                    <td>\${$row['old_amount']}</td>
                    <td>\${$row['new_amount']}</td>
                    <td>{$row['request_date']}</td>
                     <td>{$row['reason']}</td>
                    <td>
                        <a href='approve_request.php?id={$row['request_id']}&expense_id={$row['expense_id']}&new_amount={$row['new_amount']}' class='btn btn-success'>Approve</a>
                        <a href='deny_request.php?id={$row['request_id']}' class='btn btn-danger'>Deny</a>
                    </td>
                </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No pending edit requests.</p>";
    }
    ?>

</div>
</body>
</html>
