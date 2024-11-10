<?php
include 'db.php';


// manage


// Handle item addition
if (isset($_POST['submit'])) {
    $item_name = $_POST['item_name'];
    $stmt = $conn->prepare("INSERT INTO items (item_name) VALUES (?)");
    $stmt->bind_param("s", $item_name);
    $stmt->execute();
}

// Handle item deletion
if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
}

// Handle item editing
if (isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
    $new_name = $_POST['new_name'];
    $stmt = $conn->prepare("UPDATE items SET item_name = ? WHERE id = ?");
    $stmt->bind_param("si", $new_name, $edit_id);
    $stmt->execute();
}

// Fetch and display items
$result = $conn->query("SELECT * FROM items ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1 class="mt-5">Admin Panel</h1>

        <form method="POST" action="admin.php" class="mb-4">
            <div class="mb-3">
                <label for="item_name" class="form-label">Item Name</label>
                <input type="text" name="item_name" id="item_name" class="form-control" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Add Item</button>
        </form>

        <h2>Item List</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                        <td>
                            <!-- Edit button triggers modal -->
                            <button type='button' class='btn btn-warning btn-sm edit-btn' data-id='<?php echo $row['id']; ?>' data-name='<?php echo htmlspecialchars($row['item_name']); ?>' data-bs-toggle='modal' data-bs-target='#editModal'>Edit</button>
                            <!-- Delete button triggers modal -->
                            <button type='button' class='btn btn-danger btn-sm delete-btn' data-id='<?php echo $row['id']; ?>' data-bs-toggle='modal' data-bs-target='#deleteModal'>Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap Modal for Deletion -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this item?
                </div>
                <div class="modal-footer">
                    <form method="POST" action="admin.php">
                        <input type="hidden" name="delete_id" id="delete_id">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Modal for Editing -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="admin.php">
                        <input type="hidden" name="edit_id" id="edit_id">
                        <div class="mb-3">
                            <label for="new_name" class="form-label">New Item Name</label>
                            <input type="text" name="new_name" id="new_name" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
   

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
        var deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var itemId = button.getAttribute('data-id');
            var modalInput = deleteModal.querySelector('#delete_id');
            modalInput.value = itemId;
        });

        var editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var itemId = button.getAttribute('data-id');
            var itemName = button.getAttribute('data-name');
            var modalIdInput = editModal.querySelector('#edit_id');
            var modalNameInput = editModal.querySelector('#new_name');
            modalIdInput.value = itemId;
            modalNameInput.value = itemName;
        });
    </script>
</body>

</html>