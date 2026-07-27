<?php
include("config.php");
include("includes/header.php");
include("includes/navbar.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$result = mysqli_query($conn, "
SELECT a.*, u.name
FROM audit_log a
LEFT JOIN users u ON a.user_id = u.id
ORDER BY a.date ASC
");
?>

<div class="main-content">
    <div class="container mt-4">
        <h2>Audit Log</h2>

        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;"> 
            <table class="table table-bordered table-striped">
                <tr class="table-light sticky-top">
                    <th>User</th>
                    <th>Action</th>
                    <th>Table</th>
                    <th>Record ID</th>
                    <th>Date</th>
                </tr>

                <?php if ($result && mysqli_num_rows($result) > 0) { ?>
                    <?php while($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['name'] ? $row['name'] : 'Unknown'; ?></td>
                        <td><?php echo $row['action']; ?></td>
                        <td><?php echo $row['table_name']; ?></td>
                        <td><?php echo $row['record_id']; ?></td>
                        <td><?php echo $row['date']; ?></td>
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5">No audit log records found.</td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>