<?php
include("config.php");
include("includes/header.php");
include("includes/navbar.php");

if (!isset($_GET['id'])) {
    die("Order ID not found.");
}

$order_id = $_GET['id'];


$order_query = "
    SELECT po.id, po.order_date, po.status, s.name AS supplier_name
    FROM purchase_orders po
    JOIN suppliers s ON po.supplier_id = s.id
    WHERE po.id = '$order_id'
";
$order_result = mysqli_query($conn, $order_query);
$order = mysqli_fetch_assoc($order_result);

if (!$order) {
    die("Purchase order not found.");
}


$items_query = "
    SELECT poi.quantity, m.name
    FROM purchase_order_items poi
    JOIN medicines m ON poi.medicine_id = m.id
    WHERE poi.purchase_order_id = '$order_id'
";
$items_result = mysqli_query($conn, $items_query);
?>

<div class="main-content">
    <div class="container mt-4">
        <h2>View Purchase Order</h2>

        <div class="card mb-3">
            <div class="card-body">
                <p><strong>Order ID:</strong> <?php echo $order['id']; ?></p>
                <p><strong>Supplier:</strong> <?php echo $order['supplier_name']; ?></p>
                <p><strong>Order Date:</strong> <?php echo $order['order_date']; ?></p>
                <p><strong>Status:</strong> <?php echo $order['status']; ?></p>
            </div>
        </div>

        <h4>Order Items</h4>
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
                        <td colspan="2">No items found for this order.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <a href="purchase_orders.php" class="btn btn-secondary">Back</a>
    </div>
</div>

<?php include("includes/footer.php"); ?>