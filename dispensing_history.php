<?php
session_start();
include("config.php");
include("includes/header.php");

/* choose navbar automatically */
if($_SESSION['role']=="manager"){
    include("includes/navbar.php");
}else{
    include("includes/staff_navbar.php");
}

$query = "
SELECT 
d.id,
p.patient_name,
p.doctor_name,
m.name AS medicine_name,
pi.quantity,
u.name AS staff_name,
d.dispense_date

FROM dispensing d

JOIN prescriptions p ON d.prescription_id = p.id
JOIN prescription_items pi ON pi.prescription_id = p.id
JOIN medicines m ON pi.medicine_id = m.id
JOIN users u ON d.user_id = u.id

ORDER BY d.dispense_date ASC
";

$result = mysqli_query($conn,$query);
?>

<div class="main-content">
<div class="container mt-4">

<h2>Dispensing History</h2>

<table class="table table-striped table-bordered">

<tr>
<th>ID</th>
<th>Patient</th>
<th>Doctor</th>
<th>Medicine</th>
<th>Qty</th>
<th>Dispensed By</th>
<th>Date</th>
</tr>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<tr>

<td><?php echo $row['id']; ?></td>
<td><?php echo $row['patient_name']; ?></td>
<td><?php echo $row['doctor_name']; ?></td>
<td><?php echo $row['medicine_name']; ?></td>
<td><?php echo $row['quantity']; ?></td>
<td><?php echo $row['staff_name']; ?></td>
<td><?php echo $row['dispense_date']; ?></td>

</tr>

<?php } ?>

</table>

</div>
</div>

<?php include("includes/footer.php"); ?>