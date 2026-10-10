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

// Generate a secure CSRF token.
if (empty($_SESSION['medicine_edit_csrf'])) {
    $_SESSION['medicine_edit_csrf'] = bin2hex(random_bytes(32));
}

// Verify the token before processing form submissions.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $submittedToken = $_POST['csrf_token'] ?? '';

    if (
        !is_string($submittedToken) ||
        !hash_equals(
            $_SESSION['medicine_edit_csrf'],
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

// Validate the medicine ID.
$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if (!$id) {
    die("Invalid medicine ID.");
}

$message = "";

// Load medicine securely.
$stmt = $conn->prepare(
    "SELECT * FROM medicines WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$medicine = $result->fetch_assoc();

$stmt->close();

if (!$medicine) {
    die("Medicine not found.");
}

// Update medicine securely.
if (isset($_POST['update_medicine'])) {

    $name = trim((string) ($_POST['name'] ?? ''));
    $type = trim((string) ($_POST['type'] ?? ''));
    $strength = trim((string) ($_POST['strength'] ?? ''));
    $dosage_form = trim((string) ($_POST['dosage_form'] ?? ''));

    $supplier_id = filter_input(
        INPUT_POST,
        'supplier_id',
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    // Validate required fields.
    if (
        $name === '' ||
        $type === '' ||
        $strength === '' ||
        $dosage_form === '' ||
        !$supplier_id
    ) {
        $message = "Please complete all fields correctly.";

    } else {

        // Use prepared statement to prevent SQL injection.
        $stmt = $conn->prepare(
            "UPDATE medicines
             SET name = ?,
                 type = ?,
                 strength = ?,
                 dosage_form = ?,
                 supplier_id = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "ssssii",
            $name,
            $type,
            $strength,
            $dosage_form,
            $supplier_id,
            $id
        );

        if ($stmt->execute()) {

            $message = "Medicine updated successfully.";

            // Update displayed information.
            $medicine['name'] = $name;
            $medicine['type'] = $type;
            $medicine['strength'] = $strength;
            $medicine['dosage_form'] = $dosage_form;
            $medicine['supplier_id'] = $supplier_id;

        } else {
            $message = "Unable to update medicine.";
        }

        $stmt->close();
    }
}

// Escape data before displaying it in HTML.
function medicineEscape($value) {
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<div class="main-content">
    <div class="container mt-4">

        <h2>Edit Medicine</h2>

        <?php if ($message != "") { ?>
            <div class="alert alert-info">
                <?php echo medicineEscape($message); ?>
            </div>
        <?php } ?>

        <form method="POST">

    <!-- CSRF security token -->
    <input
        type="hidden"
        name="csrf_token"
        value="<?php echo htmlspecialchars(
            $_SESSION['medicine_edit_csrf'],
            ENT_QUOTES,
            'UTF-8'
        ); ?>"
    >

            <div class="mb-3">
                <label>Medicine Name</label>
                <input
                    type="text"
                    name="name"
                    class="form-control"
                    value="<?php echo medicineEscape($medicine['name']); ?>"
                    required
                >
            </div>

            <div class="mb-3">
                <label>Type</label>
                <input
                    type="text"
                    name="type"
                    class="form-control"
                    value="<?php echo medicineEscape($medicine['type']); ?>"
                    required
                >
            </div>

            <div class="mb-3">
                <label>Strength</label>
                <input
                    type="text"
                    name="strength"
                    class="form-control"
                    value="<?php echo medicineEscape($medicine['strength']); ?>"
                    required
                >
            </div>

            <div class="mb-3">
                <label>Dosage Form</label>
                <input
                    type="text"
                    name="dosage_form"
                    class="form-control"
                    value="<?php echo medicineEscape($medicine['dosage_form']); ?>"
                    required
                >
            </div>

            <div class="mb-3">
                <label>Supplier</label>

                <select
                    name="supplier_id"
                    class="form-control"
                    required
                >
                    <option value="">Select Supplier</option>

                    <?php
                    $suppliers = mysqli_query(
                        $conn,
                        "SELECT id, name FROM suppliers"
                    );

                    while ($supplier = mysqli_fetch_assoc($suppliers)) {

                        $selected = (
                            $supplier['id'] == $medicine['supplier_id']
                        ) ? "selected" : "";
                    ?>

                        <option
                            value="<?php echo (int) $supplier['id']; ?>"
                            <?php echo $selected; ?>
                        >
                            <?php echo medicineEscape($supplier['name']); ?>
                        </option>

                    <?php } ?>

                </select>
            </div>

            <button
                type="submit"
                name="update_medicine"
                class="btn btn-success"
            >
                Update Medicine
            </button>

            <a href="medicines.php" class="btn btn-secondary">
                Back
            </a>

        </form>

    </div>
</div>

<?php include("includes/footer.php"); ?>