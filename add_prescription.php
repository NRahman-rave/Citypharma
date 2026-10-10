<?php
session_start();
include("config.php");
include("includes/header.php");
include("includes/staff_navbar.php");


$message = "";

if (isset($_POST['create_prescription'])) {

    $patient_name = $_POST['patient_name'];
    $doctor_name = $_POST['doctor_name'];
    $medicine_id = $_POST['medicine_id'];
    $quantity = $_POST['quantity'];

    $user_id = $_SESSION['user_id'];

    $insert_prescription = mysqli_query($conn, "
        INSERT INTO prescriptions
        (patient_name, doctor_name, date_created, created_by)
        VALUES
        ('$patient_name', '$doctor_name', NOW(), '$user_id')
    ");

    if ($insert_prescription) {

        $prescription_id = mysqli_insert_id($conn);

        $insert_item = mysqli_query($conn, "
            INSERT INTO prescription_items
            (prescription_id, medicine_id, quantity)
            VALUES
            ('$prescription_id', '$medicine_id', '$quantity')
        ");

        if ($insert_item) {
            echo "<div class='alert alert-success'>Prescription saved successfully.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error saving prescription items: " . mysqli_error($conn) . "</div>";
        }

    } else {
        echo "<div class='alert alert-danger'>Error saving prescription: " . mysqli_error($conn) . "</div>";
    }
}
?>

<div class="main-content">
    <div class="container mt-4">
        <h2>Create Prescription</h2>

        <?php if ($message != "") { ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php } ?>

        <form method="POST">
            <div class="mb-3">
                <label>Patient Name</label>
                <input type="text" name="patient_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Doctor Name</label>
                <input type="text" name="doctor_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Medicine</label>
                <select name="medicine_id" class="form-control" required>
                    <option value="">Select Medicine</option>
                    <?php
                    $medicines = mysqli_query($conn, "SELECT id, name FROM medicines ORDER BY name ASC");
                    while ($medicine = mysqli_fetch_assoc($medicines)) {
                    ?>
                        <option value="<?php echo $medicine['id']; ?>">
                            <?php echo $medicine['name']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Quantity</label>
                <input type="number" name="quantity" class="form-control" min="1" required>
            </div>

            <button type="submit" name="create_prescription" class="btn btn-primary">
                Save Prescription
            </button>
        </form>
    </div>
</div>

<?php include("includes/footer.php"); ?>