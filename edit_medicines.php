<?php
include("config.php");
include("includes/header.php");
include("includes/navbar.php");

if (!isset($_GET['id'])) {
    die("Medicine ID not found.");
}

$id = $_GET['id'];
$message = "";


$query = "SELECT * FROM medicines WHERE id = '$id'";
$result = mysqli_query($conn, $query);
$medicine = mysqli_fetch_assoc($result);

if (!$medicine) {
    die("Medicine not found.");
}


if (isset($_POST['update_medicine'])) {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $strength = $_POST['strength'];
    $dosage_form = $_POST['dosage_form'];
    $supplier_id = $_POST['supplier_id'];

    $update_query = "
        UPDATE medicines
        SET name = '$name',
            type = '$type',
            strength = '$strength',
            dosage_form = '$dosage_form',
            supplier_id = '$supplier_id'
        WHERE id = '$id'
    ";

    if (mysqli_query($conn, $update_query)) {
        $message = "Medicine updated successfully.";

        $result = mysqli_query($conn, "SELECT * FROM medicines WHERE id = '$id'");
        $medicine = mysqli_fetch_assoc($result);
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}
?>

<div class="main-content">
    <div class="container mt-4">
        <h2>Edit Medicine</h2>

        <?php if ($message != "") { ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php } ?>

        <form method="POST">
            <div class="mb-3">
                <label>Medicine Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo $medicine['name']; ?>" required>
            </div>

            <div class="mb-3">
                <label>Type</label>
                <input type="text" name="type" class="form-control" value="<?php echo $medicine['type']; ?>" required>
            </div>

            <div class="mb-3">
                <label>Strength</label>
                <input type="text" name="strength" class="form-control" value="<?php echo $medicine['strength']; ?>" required>
            </div>

            <div class="mb-3">
                <label>Dosage Form</label>
                <input type="text" name="dosage_form" class="form-control" value="<?php echo $medicine['dosage_form']; ?>" required>
            </div>

            <div class="mb-3">
                <label>Supplier</label>
                <select name="supplier_id" class="form-control" required>
                    <option value="">Select Supplier</option>
                    <?php
                    $suppliers = mysqli_query($conn, "SELECT id, name FROM suppliers");
                    while ($supplier = mysqli_fetch_assoc($suppliers)) {
                        $selected = ($supplier['id'] == $medicine['supplier_id']) ? "selected" : "";
                    ?>
                        <option value="<?php echo $supplier['id']; ?>" <?php echo $selected; ?>>
                            <?php echo $supplier['name']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <button type="submit" name="update_medicine" class="btn btn-success">Update Medicine</button>
            <a href="medicines.php" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>

<?php include("includes/footer.php"); ?>