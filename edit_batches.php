<?php
session_start();
include("config.php");
include("includes/header.php");
include("includes/staff_navbar.php");


if (!isset($_GET['prescription_id'])) {
    die("Prescription ID missing.");
}

$prescription_id = $_GET['prescription_id'];


$prescription_query = "
SELECT p.*, u.name AS staff_name
FROM prescriptions p
LEFT JOIN users u ON p.created_by = u.id
WHERE p.id = '$prescription_id'
";

$prescription_result = mysqli_query($conn, $prescription_query);
$prescription = mysqli_fetch_assoc($prescription_result);


$items_query = "
SELECT pi.*, m.name AS medicine_name
FROM prescription_items pi
JOIN medicines m ON pi.medicine_id = m.id
WHERE pi.prescription_id = '$prescription_id'
";

$items_result = mysqli_query($conn, $items_query);


if (isset($_POST['dispense'])) {

    while ($item = mysqli_fetch_assoc($items_result)) {

        $medicine_id = $item['medicine_id'];
        $qty_needed = $item['quantity'];

        
        $batch_query = "
        SELECT * FROM batches
        WHERE medicine_id = '$medicine_id'
        AND quantity > 0
        ORDER BY expiry_date ASC
        ";

        $batch_result = mysqli_query($conn, $batch_query);

        while ($batch = mysqli_fetch_assoc($batch_result) && $qty_needed > 0) {

            $batch_id = $batch['id'];
            $available = $batch['quantity'];

            if ($available >= $qty_needed) {

                mysqli_query($conn,"
                UPDATE batches
                SET quantity = quantity - $qty_needed
                WHERE id = '$batch_id'
                ");

                $qty_needed = 0;

            } else {

                mysqli_query($conn,"
                UPDATE batches
                SET quantity = 0
                WHERE id = '$batch_id'
                ");

                $qty_needed -= $available;
            }
        }

      
        mysqli_query($conn,"
        INSERT INTO dispensing
        (prescription_id, user_id, dispense_date)
        VALUES
        ('$prescription_id','".$_SESSION['user_id']."', NOW())
        ");
    }

    echo "<div class='alert alert-success'>Medicine dispensed successfully.</div>";
}
?>

<div class="main-content">
<div class="container mt-4">

<h2>Dispense Medicine</h2>

<div class="card mb-3">
<div class="card-body">

<p><strong>Patient:</strong> <?php echo $prescription['patient_name']; ?></p>
<p><strong>Doctor:</strong> <?php echo $prescription['doctor_name']; ?></p>

</div>
</div>

<h4>Medicines</h4>

<table class="table table-bordered">

<tr>
<th>Medicine</th>
<th>Quantity</th>
</tr>

<?php
$items_result = mysqli_query($conn,$items_query);

while($row=mysqli_fetch_assoc($items_result)){
?>

<tr>
<td><?php echo $row['medicine_name']; ?></td>
<td><?php echo $row['quantity']; ?></td>
</tr>

<?php } ?>

</table>

<form method="POST">
<button class="btn btn-success" name="dispense">
Confirm Dispense
</button>
</form>

</div>
</div>

<?php include("includes/footer.php"); ?>