<?php
include("config.php");
include("includes/header.php");
include("includes/navbar.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

if (isset($_POST['add_medicine'])) {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $strength = $_POST['strength'];
    $dosage_form = $_POST['dosage_form'];
    $selling_price = $_POST['selling_price'];
    $supplier_id = $_POST['supplier_id'];

    $insert_query = "
        INSERT INTO medicines
        (name, type, strength, dosage_form, selling_price, supplier_id)
        VALUES
        ('$name', '$type', '$strength', '$dosage_form', '$selling_price', '$supplier_id')
    ";

    $result = mysqli_query($conn, $insert_query);

    if ($result) {
        $medicine_id = mysqli_insert_id($conn);

        mysqli_query($conn, "
         INSERT INTO audit_log (user_id, action, table_name, record_id, date)
         VALUES ('".$_SESSION['user_id']."', 'INSERT', 'medicines', '$medicine_id', NOW())
    ");

        $message = "<div class='alert alert-success'>Medicine added successfully.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<div class="main-content">
    <div class="container mt-4">
        <h2>Add Medicine</h2>

        <?php echo $message; ?>

        <form method="POST">
            <div class="mb-3">
                <label>Medicine Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Type</label>
                <input type="text" name="type" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Strength</label>
                <input type="text" name="strength" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Dosage Form</label>
                <input type="text" name="dosage_form" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Selling Price (£)</label>
                <input type="number" step="0.01" name="selling_price" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Supplier</label>
                <select name="supplier_id" class="form-control" required>
                    <option value="">Select Supplier</option>
                    <?php
                    $suppliers = mysqli_query($conn, "SELECT id, name FROM suppliers ORDER BY name ASC");
                    while ($supplier = mysqli_fetch_assoc($suppliers)) {
                    ?>
                        <option value="<?php echo $supplier['id']; ?>">
                            <?php echo $supplier['name']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <button type="submit" name="add_medicine" class="btn btn-primary">Add Medicine</button>
            <a href="medicines.php" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>

<?php include("includes/footer.php"); ?>