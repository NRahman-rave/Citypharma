<?php

include("config.php");
include("includes/header.php");
include("includes/navbar.php");
?>

<div class="main-content">
    <div class="container mt-4">
        <h2>Manage Batches</h2>
        <a href="batches.php" class="btn btn-primary mb-3">+ Add Batch</a>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Medicine Name</th>
<th>Batch Number</th>
<th>Quantity</th>
<th>Expiry Date</th>
<th>Status</th>
<th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "
                    SELECT b.id, m.name, b.batch_number, b.quantity, b.expiry_date
                    FROM batches b
                    JOIN medicines m ON b.medicine_id = m.id
                    ORDER BY b.expiry_date ASC
                ";

                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $today = date('Y-m-d');
                        $status = "Safe";

                        if ($row['expiry_date'] < $today) {
                            $status = "Expired";
                        } elseif ($row['expiry_date'] <= date('Y-m-d', strtotime('+30 days'))) {
                            $status = "Near Expiry";
                        }
                ?>
                    <tr>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['batch_number']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td><?php echo $row['expiry_date']; ?></td>
                        <td><?php echo $status; ?></td>
                        
    <td>
    <a href="edit_batches.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
    <a href="delete_batches.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger"
       onclick="return confirm('Are you sure you want to delete this batch?');">Delete</a>
</td>
                    </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='6'>No batches found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("includes/footer.php"); ?>