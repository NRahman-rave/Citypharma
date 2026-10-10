<?php
session_start();
include("config.php");
include("includes/header.php");
include("includes/staff_navbar.php");

if (!isset($_SESSION['name'])) {
    header("Location: login.php");
    exit();
}

$query = "
    SELECT p.id, p.patient_name, p.doctor_name, p.date_created, u.name AS staff_name
    FROM prescriptions p
    LEFT JOIN users u ON p.created_by = u.id
    ORDER BY p.date_created ASC
";

$result = mysqli_query($conn, $query);
?>

<div class="main-content">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Prescriptions</h2>
            <a href="add_prescription.php" class="btn btn-primary">+ Create Prescription</a>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient Name</th>
                    <th>Doctor Name</th>
                    <th>Date Created</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['patient_name']; ?></td>
                            <td><?php echo $row['doctor_name']; ?></td>
                            <td><?php echo $row['date_created']; ?></td>
                            <td><?php echo $row['staff_name'] ? $row['staff_name'] : '-'; ?></td>
                            <td>
                                <a href="view_prescription.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">View</a>
                                <a href="dispense.php?prescription_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success">Dispense</a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="6">No prescriptions found</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("includes/footer.php"); ?>