<?php
include("config.php");
include("includes/header.php");
include("includes/navbar.php");

if(isset($_POST['create']))
{

$supplier_id=$_POST['supplier_id'];

$date=date("Y-m-d");

mysqli_query($conn,
"INSERT INTO purchase_orders (supplier_id,order_date)
VALUES ('$supplier_id','$date')"
);

$order_id=mysqli_insert_id($conn);

mysqli_query($conn, "
    INSERT INTO audit_log (user_id, action, table_name, record_id, date)
    VALUES ('".$_SESSION['user_id']."', 'ORDER_CREATED', 'purchase_orders', '$order_id', NOW())
");

header("Location:add_order_items.php?order_id=$order_id");

}

?>

<div class="main-content">
<div class="container mt-4">

<h2>Create Purchase Order</h2>

<form method="POST">

<label>Supplier</label>

<select name="supplier_id" class="form-control">

<?php

$suppliers=mysqli_query($conn,"SELECT * FROM suppliers");

while($s=mysqli_fetch_assoc($suppliers))
{

echo "<option value='$s[id]'>$s[name]</option>";

}

?>

</select>

<br>

<button name="create" class="btn btn-primary">
Create Order
</button>

</form>

</div>
</div>

<?php include("includes/footer.php"); ?>