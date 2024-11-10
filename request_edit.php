<?php include 'db.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Expense Edit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Request Expense Edit</h1>

        <?php
        $expense_id = $_GET['id'];
        $expense = $conn->query("SELECT * FROM expenses WHERE id = $expense_id")->fetch_assoc();
        ?>

        <form action="process_request.php" method="POST">
            <input type="hidden" name="expense_id" value="<?php echo $expense_id; ?>">
            <div class="mb-3">
                <label for="amount" class="form-label">New Amount</label>
                <input type="number" step="0.01" name="amount" id="amount" class="form-control" value="<?php echo $expense['amount']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="reason" class="form-label">Reason for Edit</label>
                <textarea name="reason" id="reason" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Request</button>
        </form>
    </div>
</body>
</html>

