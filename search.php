<?php
include 'db.php';
include 'includes/header.php';

$search = $_POST['search'] ?? '';
$sql = "SELECT * FROM items WHERE item_name LIKE ?";
$stmt = $conn->prepare($sql);
$search_param = "%$search%";
$stmt->bind_param("s", $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container">
    <h1>Search Results</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Item</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo $row['item_name']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
