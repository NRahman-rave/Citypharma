<?php
session_start();
include("config.php");
include("includes/header.php");
include("includes/staff_navbar.php");


$query = "
    SELECT 
        pay.id,
        pay.prescription_id,
        p.patient_name,
        pay.amount,
        pay.payment_method,
        pay.reference_no,
        pay.payment_date,
        u.name AS staff_name
    FROM payments pay
    JOIN prescriptions p ON pay.prescription_id = p.id
    JOIN users u ON pay.user_id = u.id
    ORDER BY pay.payment_date ASC
";

$result = mysqli_query($conn, $query);
?>

<div class="main-content">
    <div class="container mt-4">
        <h2>Payments</h2>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Prescription ID</th>
                    <th>Patient</th>
                    <th>Amount (£)</th>
                    <th>Method</th>
                    <th>Reference</th>
                    <th>Date</th>
                    <th>Processed By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['prescription_id']; ?></td>
                            <td><?php echo $row['patient_name']; ?></td>
                            <td><?php echo number_format($row['amount'], 2); ?></td>
                            <td><?php echo $row['payment_method']; ?></td>
                            <td><?php echo $row['reference_no']; ?></td>
                            <td><?php echo $row['payment_date']; ?></td>
                            <td><?php echo $row['staff_name']; ?></td>
                            <td>
                                <a href="payment_receipt.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">
                                    View Receipt
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="9">No payments found.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("includes/footer.php"); ?>