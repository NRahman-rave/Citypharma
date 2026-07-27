<?php
session_start();
include("config.php");

if(!isset($_GET['id'])){
    die("Order ID missing");
}

$order_id = $_GET['id'];


$order_query = "
SELECT po.id,
       po.order_date,
       po.status,
       s.name AS supplier_name,
       s.phone,
       s.email,
       s.address
FROM purchase_orders po
JOIN suppliers s ON po.supplier_id = s.id
WHERE po.id='$order_id'
";

$order_result = mysqli_query($conn,$order_query);
$order = mysqli_fetch_assoc($order_result);


$items_query = "
SELECT m.name,
       poi.quantity
FROM purchase_order_items poi
JOIN medicines m ON poi.medicine_id = m.id
WHERE poi.purchase_order_id='$order_id'
";

$items_result = mysqli_query($conn,$items_query);


$total_items = 0;

while($row = mysqli_fetch_assoc($items_result)){
    $total_items += $row['quantity'];
}

mysqli_data_seek($items_result,0);
?>

<!DOCTYPE html>
<html>

<head>

<title>Purchase Order Receipt</title>

<style>

body{
font-family:Arial;
margin:40px;
}

.receipt-box{
max-width:800px;
margin:auto;
padding:30px;
border:1px solid #eee;
box-shadow:0 0 10px rgba(0,0,0,.15);
}

h1{
text-align:center;
}

table{
width:100%;
border-collapse:collapse;
margin-top:20px;
}

table th,
table td{
border:1px solid #ddd;
padding:10px;
text-align:left;
}

th{
background:#f4f4f4;
}

.total-box{
text-align:right;
margin-top:20px;
font-size:18px;
}

.print-btn{
margin-top:20px;
}

</style>

</head>

<body>

<div class="receipt-box">

<h1>CityPharma</h1>

<p>
<strong>Purchase Order Receipt</strong>
</p>

<hr>

<p>
<strong>Order ID:</strong> <?php echo $order['id']; ?><br>

<strong>Date:</strong> <?php echo $order['order_date']; ?><br>

<strong>Status:</strong> <?php echo $order['status']; ?>
</p>

<hr>

<h3>Supplier Details</h3>

<p>

<?php echo $order['supplier_name']; ?><br>

<?php echo $order['phone']; ?><br>

<?php echo $order['email']; ?><br>

<?php echo $order['address']; ?>

</p>

<hr>

<h3>Order Items</h3>

<table>

<tr>

<th>Medicine</th>

<th>Quantity</th>

</tr>

<?php
while($item = mysqli_fetch_assoc($items_result)){
?>

<tr>

<td><?php echo $item['name']; ?></td>

<td><?php echo $item['quantity']; ?></td>

</tr>

<?php
}
?>

</table>

<div class="total-box">

<strong>Total Items:</strong> <?php echo $total_items; ?>

</div>

<button onclick="window.print()" class="print-btn">
Print Receipt
</button>

</div>

</body>

</html>