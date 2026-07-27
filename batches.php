<?php
include("config.php");
include("includes/header.php");
include("includes/navbar.php");

$message = "";

if (isset($_POST['add_batch'])) {
    $medicine_id = $_POST['medicine_id'];
    $batch_number = $_POST['batch_number'];
    $quantity = $_POST['quantity'];
    $expiry_date = $_POST['expiry_date'];

    $sql = "INSERT INTO batches (medicine_id, batch_number, quantity, expiry_date)
            VALUES ('$medicine_id', '$batch_number', '$quantity', '$expiry_date')";

    if (mysqli_query($conn, $sql)) {
        $message = "Batch added successfully.";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}
?>

<div class="main-content">
    <div class="container mt-4">
        <h2>Add Batch</h2>

        <a href="manage_batches.php" class="btn btn-success mb-3">Manage Batches</a>

        <?php if ($message != "") { ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php } ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Medicine</label>
                <select name="medicine_id" class="form-control" required>
                    <option value="">Select Medicine</option>
                    <?php
                    $medicines = mysqli_query($conn, "SELECT id, name FROM medicines");
                    while ($row = mysqli_fetch_assoc($medicines)) {
                    ?>
                        <option value="<?php echo $row['id']; ?>">
                            <?php echo $row['name']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Batch Number</label>
                <input type="text" name="batch_number" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Quantity(cases)</label>
                <input type="number" name="quantity" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Expiry Date</label>
                <input type="date" name="expiry_date" class="form-control" required>
            </div>

            <button type="submit" name="add_batch" class="btn btn-primary">
                Add Batch
            </button>
        </form>
    </div>
</div>

<?php include("includes/footer.php"); ?>