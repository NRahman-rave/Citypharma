<?php
include("config.php");
include("includes/header.php");
include("includes/navbar.php");

$message = "";

if (isset($_POST['add_supplier'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $query = "INSERT INTO suppliers (name, phone, email, address)
              VALUES ('$name', '$phone', '$email', '$address')";

    if (mysqli_query($conn, $query)) {
        $message = "Supplier added successfully.";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}
?>

<div class="main-content">
    <div class="container mt-4">
        <h2>Add Supplier</h2>

        <?php if ($message != "") { ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php } ?>

        <form method="POST">
            <div class="mb-3">
                <label>Supplier Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control">
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control">
            </div>

            <div class="mb-3">
                <label>Address</label>
                <textarea name="address" class="form-control" rows="3"></textarea>
            </div>

            <button type="submit" name="add_supplier" class="btn btn-primary">Add Supplier</button>
            <a href="suppliers.php" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>

<?php include("includes/footer.php"); ?>