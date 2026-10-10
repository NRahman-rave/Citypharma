<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Require login.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Only managers can add or edit medicines.
if (($_SESSION['role'] ?? '') !== 'manager') {
    http_response_code(403);
    exit("Access denied. Managers only.");
}

if (empty($_SESSION['medicine_add_csrf'])) {
    $_SESSION['medicine_add_csrf'] = bin2hex(random_bytes(32));
}

// Verify CSRF token on form submission.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $submittedToken = $_POST['csrf_token'] ?? '';

    if (
        !is_string($submittedToken) ||
        !hash_equals(
            $_SESSION['medicine_add_csrf'],
            $submittedToken
        )
    ) {
        http_response_code(403);
        exit("Invalid security token. Refresh and try again.");
    }
}

include("config.php");
include("includes/header.php");
include("includes/navbar.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

if (isset($_POST['add_medicine'])) {
    $name = trim((string) ($_POST['name'] ?? ''));
    $type = trim((string) ($_POST['type'] ?? ''));
    $strength = trim((string) ($_POST['strength'] ?? ''));
    $dosage_form = trim((string) ($_POST['dosage_form'] ?? ''));
    $selling_price = $_POST['selling_price'] ?? '';
    $supplier_id = filter_input(
        INPUT_POST,
        'supplier_id',
        FILTER_VALIDATE_INT
    );

    // Validate submitted medicine information.
    if (
        $name === '' || $type === '' ||
        $strength === '' || $dosage_form === '' ||
        !is_numeric($selling_price) ||
        (float) $selling_price < 0 ||
        !$supplier_id || $supplier_id < 1
    ) {
        $message = "<div class='alert alert-danger'>Please enter valid medicine details.</div>";
    } else {
        $price = (float) $selling_price;

        // Prepared statements prevent SQL injection.
        $stmt = $conn->prepare(
            "INSERT INTO medicines
             (name, type, strength, dosage_form, selling_price, supplier_id)
             VALUES (?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            'ssssdi',
            $name,
            $type,
            $strength,
            $dosage_form,
            $price,
            $supplier_id
        );

        if ($stmt->execute()) {
            $medicine_id = $conn->insert_id;
            $user_id = (int) $_SESSION['user_id'];

            // Record the action securely in the audit log.
            $audit_stmt = $conn->prepare(
                "INSERT INTO audit_log
                 (user_id, action, table_name, record_id, date)
                 VALUES (?, 'INSERT', 'medicines', ?, NOW())"
            );

            $audit_stmt->bind_param(
                'ii',
                $user_id,
                $medicine_id
            );

            $audit_stmt->execute();
            $audit_stmt->close();

            $message = "<div class='alert alert-success'>Medicine added successfully.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Unable to add medicine.</div>";
        }

        $stmt->close();
    }
}
?>

<div class="main-content">
    <div class="container mt-4">
        <h2>Add Medicine</h2>

        <?php echo $message; ?>

        <form method="POST">

    <!-- CSRF Protection -->
    <input
        type="hidden"
        name="csrf_token"
        value="<?php echo htmlspecialchars(
            $_SESSION['medicine_add_csrf'],
            ENT_QUOTES,
            'UTF-8'
        ); ?>"
    >
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