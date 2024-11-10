<?php
session_start();
include 'db.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// User is logged in; proceed with the rest of the admin panel code
$user_id = $_SESSION['user_id']; // Retrieve user ID from session

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Family Expenses Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .logout-button {
            position: fixed;
            top: 20px; /* Adjust as needed */
            right: 20px; /* Adjust as needed */
            z-index: 1000; /* Ensures it stays on top of other content */
        }
    </style>
</head>
<body>
<div class="container"> 
    
    <h1 class="mt-5">Family Expenses Tracker</h1>
    
    <a href="logout.php" class="btn btn-danger mb-4 logout-button">Logout</a>
    <form method="POST" action="index.php" class="mb-4">
        <div class="mb-3">
            <label for="item" class="form-label">Select Item</label>
            <select name="item_id" id="item" class="form-select">
                <?php
                // Fetch items for the dropdown
                $result = $conn->query("SELECT * FROM items");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['item_name']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" name="amount" id="amount" class="form-control" required>
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Add Expense</button>
    </form>

    <?php
    // Insert the expense into the database
    if (isset($_POST['submit'])) {
        $item_id = $_POST['item_id'];
        $amount = $_POST['amount'];
        $date = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("INSERT INTO expenses (item_id, amount, date, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("idsi", $item_id, $amount, $date, $user_id);
        $stmt->execute();
    }

    // Fetch and display expenses for the logged-in user
    $stmt = $conn->prepare("SELECT expenses.id, items.item_name, expenses.amount, expenses.date 
                            FROM expenses 
                            JOIN items ON expenses.item_id = items.id 
                            WHERE expenses.user_id = ? 
                            ORDER BY expenses.date DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    ?>

    <h2>Expense Records</h2>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Item</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['item_name']}</td>
                <td>\${$row['amount']}</td>
                <td>{$row['date']}</td>
                <td><a href='request_edit.php?id={$row['id']}' class='btn btn-warning'>Request Edit</a></td>
            </tr>";
        }
        ?>
        </tbody>
    </table>
</div>
</body>
</html>
