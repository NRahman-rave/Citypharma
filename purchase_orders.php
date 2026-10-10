<?php
include("config.php");
include("includes/header.php");
include("includes/navbar.php");
?>

<div class="main-content">
<div class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-3">
<h2>Purchase Orders</h2>
<a href="add_purchase_order.php" class="btn btn-primary">+ New Order</a>
</div>

<table class="table table-bordered">

<tr>
<th>ID</th>
<th>Supplier</th>
<th>Date</th>
<th>Status</th>
<th>Actions</th>
</tr>

<?php

$query = "
SELECT po.id,
       s.name AS supplier,
       po.order_date,
       po.status

FROM purchase_orders po
JOIN suppliers s ON po.supplier_id = s.id

ORDER BY po.id ASC
";

$result = mysqli_query($conn,$query);

while($row=mysqli_fetch_assoc($result))
{
?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['supplier']; ?></td>

<td><?php echo $row['order_date']; ?></td>

<td><?php echo $row['status']; ?></td>

<td>

<a href="view_order.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">
View
</a>

<a href="order_receipt.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary btn-sm">
Receipt
</a>

<a href="receive_order.php?id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">
Receive
</a>

</td>

</tr>

<?php } ?>

</table>

</div>
</div>

<?php include("includes/footer.php"); ?>