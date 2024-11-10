<?php
include '../db.php';

// Fetch the item names and their total amounts from the expenses table
$query = "
    SELECT i.item_name, SUM(e.amount) as total_amount
    FROM expenses e
    JOIN items i ON e.item_id = i.id
    GROUP BY i.item_name
";
$result = $conn->query($query);

$items = [];
$amounts = [];

while ($row = $result->fetch_assoc()) {
    $items[] = $row['item_name'];
    $amounts[] = $row['total_amount'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin View - Graphs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Monthly Expenses Overview</h1>
    <canvas id="expensesChart" width="400" height="200"></canvas>
</div>

<script>
    var ctx = document.getElementById('expensesChart').getContext('2d');
    var expensesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($items); ?>,
            datasets: [{
                label: 'Total Amount Spent',
                data: <?php echo json_encode($amounts); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
</body>
</html>
