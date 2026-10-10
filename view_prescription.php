<?php
session_start();
include("config.php");
include("includes/header.php");
include("includes/staff_navbar.php");

if (!isset($_SESSION['name'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("Prescription ID not found.");
}

$id = $_GET['id'];

$prescription_query = "
    SELECT p.*, u.name AS staff_name
    FROM prescriptions p
    LEFT JOIN users u ON p.created_by = u.id
    WHERE p.id = '$id'
";
$prescription_result = mysqli_query($conn, $prescription_query);
$prescription = mysqli_fetch_assoc($prescription_result);

if (!$prescription) {
    die("Prescription not found.");
}

$items_query = "
    SELECT pi.quantity, m.name
    FROM prescription_items pi
    JOIN medicines m ON pi.medicine_id = m.id
    WHERE pi.prescription_id = '$id'
";
$items_result = mysqli_query($conn, $items_query);
?>

<div class="main-content">
    <div class="container mt-4">
        <h2>View Prescription</h2>

        <div class="card mb-3">
            <div class="card-body">
                <p><strong>Prescription ID:</strong> <?php echo $prescription['id']; ?></p>
                <p><strong>Patient Name:</strong> <?php echo $prescription['patient_name']; ?></p>
                <p><strong>Doctor Name:</strong> <?php echo $prescription['doctor_name']; ?></p>
                <p><strong>Date Created:</strong> <?php echo $prescription['date_created']; ?></p>
                <p><strong>Created By:</strong> <?php echo $prescription['staff_name'] ? $prescription['staff_name'] : '-'; ?></p>
            </div>
        </div>

        <h4>Prescription Items</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Medicine</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($items_result) > 0) { ?>
                    <?php while ($item = mysqli_fetch_assoc($items_result)) { ?>
                        <tr>
                            <td><?php echo $item['name']; ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="2">No prescription items found.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <a href="dispense.php?prescription_id=<?php echo $prescription['id']; ?>" class="btn btn-success">Dispense</a>
        <a href="prescriptions.php" class="btn btn-secondary">Back</a>
    </div>
</div>

<?php include("includes/footer.php"); ?>