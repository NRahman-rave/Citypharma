<?php
include("config.php");
include("includes/header.php");
include("includes/navbar.php");

if (!isset($_GET['id'])) {
    die("Supplier ID not found.");
}

$id = $_GET['id'];
$message = "";

$query = "SELECT * FROM suppliers WHERE id = '$id'";
$result = mysqli_query($conn, $query);
$supplier = mysqli_fetch_assoc($result);

if (!$supplier) {
    die("Supplier not found.");
}

if (isset($_POST['update_supplier'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $update_query = "
        UPDATE suppliers
        SET name = '$name',
            phone = '$phone',
            email = '$email',
            address = '$address'
        WHERE id = '$id'
    ";

    if (mysqli_query($conn, $update_query)) {
        $message = "Supplier updated successfully.";
        $result = mysqli_query($conn, "SELECT * FROM suppliers WHERE id = '$id'");
        $supplier = mysqli_fetch_assoc($result);
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}
?>

<div class="main-content">
    <div class="container mt-4">
        <h2>Edit Supplier</h2>

        <?php if ($message != "") { ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php } ?>

        <form method="POST">
            <div class="mb-3">
                <label>Supplier Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo $supplier['name']; ?>" required>
            </div>

            <div class="mb-3">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" value="<?php echo $supplier['phone']; ?>">
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo $supplier['email']; ?>">
            </div>

            <div class="mb-3">
                <label>Address</label>
                <textarea name="address" class="form-control" rows="3"><?php echo $supplier['address']; ?></textarea>
            </div>

            <button type="submit" name="update_supplier" class="btn btn-success">Update Supplier</button>
            <a href="suppliers.php" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>

<?php include("includes/footer.php"); ?>