<?php
include("config.php");

if (!isset($_GET['id'])) {
    die("Order ID not found.");
}

$id = $_GET['id'];


$order_result = mysqli_query($conn, "SELECT * FROM purchase_orders WHERE id = '$id'");
$order = mysqli_fetch_assoc($order_result);

if (!$order) {
    die("Purchase order not found.");
}

if ($order['status'] == 'Received') {
    echo "
    <script>
        alert('This order has already been received.');
        window.location.href='purchase_orders.php';
    </script>
    ";
    exit();
}


$items = mysqli_query($conn, "
    SELECT * FROM purchase_order_items
    WHERE purchase_order_id = '$id'
");

while ($i = mysqli_fetch_assoc($items)) {
    $medicine_id = $i['medicine_id'];
    $quantity = $i['quantity'];

    
    $batch_number = "PO" . $id . "-" . rand(100,999);
    $expiry_date = date('Y-m-d', strtotime('+1 year'));

    mysqli_query($conn, "
        INSERT INTO batches (medicine_id, batch_number, quantity, expiry_date)
        VALUES ('$medicine_id', '$batch_number', '$quantity', '$expiry_date')
    ");
}


mysqli_query($conn, "
    UPDATE purchase_orders
    SET status = 'Received'
    WHERE id = '$id'
");

mysqli_query($conn, "
    INSERT INTO audit_log (user_id, action, table_name, record_id, date)
    VALUES ('".$_SESSION['user_id']."', 'ORDER_RECEIVED', 'purchase_orders', '$order_id', NOW())
");

echo "
<script>
    alert('Purchase order received successfully.');
    window.location.href='purchase_orders.php';
</script>
";
?>