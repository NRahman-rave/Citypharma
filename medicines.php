<?php
include("config.php");
include("includes/header.php");
include("includes/navbar.php");
?>

<div class="main-content">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Manage Medicines</h2>
            <a href="add_medicine.php" class="btn btn-primary">+ Add Medicine</a>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>Type</th>
                    <th>Strength</th>
                    <th>Dosage Form</th>
                    <th>Supplier</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "
                    SELECT m.id, m.name, m.type, m.strength, m.dosage_form, s.name AS supplier_name
                    FROM medicines m
                    LEFT JOIN suppliers s ON m.supplier_id = s.id
                    ORDER BY m.name ASC
                ";

                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                ?>
                    <tr>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['type']; ?></td>
                        <td><?php echo $row['strength']; ?></td>
                        <td><?php echo $row['dosage_form']; ?></td>
                        <td><?php echo $row['supplier_name'] ? $row['supplier_name'] : 'No Supplier'; ?></td>
                        <td>
                            <a href="edit_medicines.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete_medicines.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger"
                               onclick="return confirm('Are you sure you want to delete this medicine?');">Delete</a>
                        </td>
                    </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='6'>No medicines found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("includes/footer.php"); ?>