<?php
session_start();
include("config.php");
include("includes/header.php");
include("includes/staff_navbar.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['prescription_id'])) {
    die("Prescription ID missing.");
}

$prescription_id = $_GET['prescription_id'];
$message = "";


$prescription_query = "
    SELECT p.*, u.name AS staff_name
    FROM prescriptions p
    LEFT JOIN users u ON p.created_by = u.id
    WHERE p.id = '$prescription_id'
";
$prescription_result = mysqli_query($conn, $prescription_query);
$prescription = mysqli_fetch_assoc($prescription_result);

if (!$prescription) {
    die("Prescription not found.");
}


$items_query = "
    SELECT pi.quantity, m.name AS medicine_name, m.selling_price
    FROM prescription_items pi
    JOIN medicines m ON pi.medicine_id = m.id
    WHERE pi.prescription_id = '$prescription_id'
";
$items_result = mysqli_query($conn, $items_query);

$total_amount = 0;
$items = [];

while ($row = mysqli_fetch_assoc($items_result)) {
    $line_total = $row['selling_price'] * $row['quantity'];
    $row['line_total'] = $line_total;
    $items[] = $row;
    $total_amount += $line_total;
}


if (isset($_POST['save_payment'])) {
    $payment_method = $_POST['payment_method'];
    $user_id = $_SESSION['user_id'];
    $reference_no = "PAY-" . date("YmdHis") . "-" . rand(100,999);

    $insert_payment = mysqli_query($conn, "
        INSERT INTO payments
        (prescription_id, amount, payment_method, reference_no, payment_date, user_id)
        VALUES
        ('$prescription_id', '$total_amount', '$payment_method', '$reference_no', NOW(), '$user_id')
    ");

    if ($insert_payment) {
        $payment_id = mysqli_insert_id($conn);

        mysqli_query($conn, "
INSERT INTO audit_log (user_id, action, table_name, record_id, date)
VALUES ('".$_SESSION['user_id']."', 'PAYMENT', 'payments', '$payment_id', NOW())
");

        header("Location: payment_receipt.php?id=" . $payment_id);
        exit();
    } else {
        $message = "<div class='alert alert-danger'>Error saving payment: " . mysqli_error($conn) . "</div>";
    }
}
?>

<div class="main-content">
    <div class="container mt-4">
        <h2>Payment</h2>

        <?php echo $message; ?>

        <div class="card mb-3">
            <div class="card-body">
                <p><strong>Prescription ID:</strong> <?php echo $prescription['id']; ?></p>
                <p><strong>Patient:</strong> <?php echo $prescription['patient_name']; ?></p>
                <p><strong>Doctor:</strong> <?php echo $prescription['doctor_name']; ?></p>
                <p><strong>Date Created:</strong> <?php echo $prescription['date_created']; ?></p>
            </div>
        </div>

        <h4>Medicines</h4>
        <table class="table table-bordered">
            <tr>
                <th>Medicine</th>
                <th>Quantity</th>
                <th>Unit Price (£)</th>
                <th>Total (£)</th>
            </tr>

            <?php foreach ($items as $item) { ?>
                <tr>
                    <td><?php echo $item['medicine_name']; ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td><?php echo number_format($item['selling_price'], 2); ?></td>
                    <td><?php echo number_format($item['line_total'], 2); ?></td>
                </tr>
            <?php } ?>

            <tr>
                <td colspan="3"><strong>Grand Total</strong></td>
                <td><strong>£<?php echo number_format($total_amount, 2); ?></strong></td>
            </tr>
        </table>

        <form method="POST">
            <div class="mb-3">
                <label>Payment Method</label>
                <select name="payment_method" class="form-control" required>
                    <option value="">Select Payment Method</option>
                    <option value="Cash">Cash</option>
                    <option value="Card">Card</option>
                </select>
            </div>

            <button type="submit" name="save_payment" class="btn btn-success">Confirm Payment</button>
            <a href="prescriptions.php" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>

<?php include("includes/footer.php"); ?>