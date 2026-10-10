<?php
session_start();
include("config.php");

if (!isset($_GET['id'])) {
    die("Payment ID missing.");
}

$id = $_GET['id'];

$payment_query = "
    SELECT pay.*, p.patient_name, p.doctor_name, u.name AS staff_name
    FROM payments pay
    JOIN prescriptions p ON pay.prescription_id = p.id
    JOIN users u ON pay.user_id = u.id
    WHERE pay.id = '$id'
";
$payment_result = mysqli_query($conn, $payment_query);
$payment = mysqli_fetch_assoc($payment_result);

if (!$payment) {
    die("Payment not found.");
}

$items_query = "
    SELECT pi.quantity, m.name AS medicine_name, m.selling_price
    FROM prescription_items pi
    JOIN medicines m ON pi.medicine_id = m.id
    WHERE pi.prescription_id = '{$payment['prescription_id']}'
";
$items_result = mysqli_query($conn, $items_query);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment Receipt</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .receipt-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1, h3 { margin-bottom: 10px; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th { background: #f5f5f5; }
        .print-btn { margin-top: 20px; }
    </style>
</head>
<body>

<div class="receipt-box">
    <h1>CityPharma</h1>
    <h3>Payment Receipt</h3>

    <p><strong>Receipt Ref:</strong> <?php echo $payment['reference_no']; ?></p>
    <p><strong>Patient:</strong> <?php echo $payment['patient_name']; ?></p>
    <p><strong>Doctor:</strong> <?php echo $payment['doctor_name']; ?></p>
    <p><strong>Payment Method:</strong> <?php echo $payment['payment_method']; ?></p>
    <p><strong>Payment Date:</strong> <?php echo $payment['payment_date']; ?></p>
    <p><strong>Processed By:</strong> <?php echo $payment['staff_name']; ?></p>

    <table>
        <tr>
            <th>Medicine</th>
            <th>Quantity</th>
            <th>Unit Price (£)</th>
            <th>Total (£)</th>
        </tr>

        <?php while ($item = mysqli_fetch_assoc($items_result)) { ?>
            <tr>
                <td><?php echo $item['medicine_name']; ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td><?php echo number_format($item['selling_price'], 2); ?></td>
                <td><?php echo number_format($item['selling_price'] * $item['quantity'], 2); ?></td>
            </tr>
        <?php } ?>

        <tr>
            <td colspan="3"><strong>Grand Total</strong></td>
            <td><strong>£<?php echo number_format($payment['amount'], 2); ?></strong></td>
        </tr>
    </table>

    <button onclick="window.print()" class="print-btn">Print Receipt</button>
</div>

</body>
</html>