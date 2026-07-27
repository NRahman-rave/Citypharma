<?php
include("config.php");
include("includes/header.php");
include("includes/navbar.php");
?>

<div class="main-content">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Manage Suppliers</h2>
            <a href="add_suppliers.php" class="btn btn-primary">+ Add Supplier</a>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Supplier Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM suppliers ORDER BY name ASC";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                ?>
                    <tr>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['phone'] ? $row['phone'] : '-'; ?></td>
                        <td><?php echo $row['email'] ? $row['email'] : '-'; ?></td>
                        <td><?php echo $row['address'] ? $row['address'] : '-'; ?></td>
                        <td>
                            
<a href="edit_suppliers.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
<a href="delete_suppliers.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger"
   onclick="return confirm('Are you sure you want to delete this supplier?');">Delete</a>
                        </td>
                    </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='5'>No suppliers found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("includes/footer.php"); ?>