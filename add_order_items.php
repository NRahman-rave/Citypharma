<?php
include("config.php");
include("includes/header.php");
include("includes/navbar.php");

$order_id=$_GET['order_id'];

if(isset($_POST['add_item']))
{

$medicine_id=$_POST['medicine_id'];
$quantity=$_POST['quantity'];

mysqli_query($conn,
"INSERT INTO purchase_order_items
(purchase_order_id,medicine_id,quantity)

VALUES
('$order_id','$medicine_id','$quantity')"
);

}

?>

<div class="main-content">
<div class="container mt-4">

<h2>Add Medicines to Order #<?php echo $order_id; ?></h2>

<form method="POST">

<select name="medicine_id" class="form-control">

<?php

$med=mysqli_query($conn,"SELECT * FROM medicines");

while($m=mysqli_fetch_assoc($med))
{

echo "<option value='$m[id]'>$m[name]</option>";

}

?>

</select>

<br>

<input type="number" name="quantity"
placeholder="Quantity"
class="form-control">

<br>

<button name="add_item" class="btn btn-success">
Add Medicine
</button>

</form>

<br>

<a href="purchase_orders.php"
class="btn btn-primary">
Finish Order
</a>

</div>
</div>

<?php include("includes/footer.php"); ?>