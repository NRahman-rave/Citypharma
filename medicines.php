<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/config.php";

// Only logged-in managers and staff can view this page.
if (
    !isset($_SESSION['user_id']) ||
    !in_array($_SESSION['role'] ?? '', ['manager', 'staff'], true)
) {
    header("Location: login.php");
    exit();
}

// Generate CSRF token.
if (empty($_SESSION['medicine_delete_csrf'])) {
    $_SESSION['medicine_delete_csrf'] = bin2hex(random_bytes(32));
}

// Escape HTML safely.
function medicineListEscape($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// Safe deletion messages.
$deleteMessages = [
    'deleted' => ['success', 'Medicine deleted successfully.'],
    'blocked' => ['warning', 'Cannot delete this medicine because it has related batch records.'],
    'not_found' => ['warning', 'Medicine not found.'],
    'invalid' => ['danger', 'Invalid medicine ID.'],
    'error' => ['danger', 'Unable to delete medicine.']
];

$deleteStatus = $_GET['delete_status'] ?? '';

include("includes/header.php");
include("includes/navbar.php");
?>

<div class="main-content">
    <div class="container mt-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Manage Medicines</h2>
            <a href="add_medicine.php" class="btn btn-primary">
                + Add Medicine
            </a>
        </div>

        <?php if (isset($deleteMessages[$deleteStatus])): ?>
            <div class="alert alert-<?php echo $deleteMessages[$deleteStatus][0]; ?>">
                <?php echo medicineListEscape($deleteMessages[$deleteStatus][1]); ?>
            </div>
        <?php endif; ?>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>Type</th>
                    <th>Strength</th>
                    <th>Dosage Form</th>
                    <th>Supplier</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php
                $query = "
                    SELECT
                        m.id,
                        m.name,
                        m.type,
                        m.strength,
                        m.dosage_form,
                        s.name AS supplier_name
                    FROM medicines m
                    LEFT JOIN suppliers s
                        ON m.supplier_id = s.id
                    ORDER BY m.name ASC
                ";

                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {

                    while ($row = mysqli_fetch_assoc($result)) {
                ?>
                    <tr>
                        <td><?php echo medicineListEscape($row['name']); ?></td>
                        <td><?php echo medicineListEscape($row['type']); ?></td>
                        <td><?php echo medicineListEscape($row['strength']); ?></td>
                        <td><?php echo medicineListEscape($row['dosage_form']); ?></td>

                        <td>
                            <?php echo medicineListEscape(
                                $row['supplier_name'] ?? 'No Supplier'
                            ); ?>
                        </td>

                        <td>
                            <a
                                href="edit_medicines.php?id=<?php echo (int) $row['id']; ?>"
                                class="btn btn-sm btn-warning"
                            >
                                Edit
                            </a>

                            <?php if ($_SESSION['role'] === 'manager'): ?>

                                <form
                                    method="POST"
                                    action="delete_medicines.php"
                                    style="display:inline;"
                                    onsubmit="return confirm('Are you sure you want to delete this medicine?');"
                                >
                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?php echo (int) $row['id']; ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?php echo medicineListEscape($_SESSION['medicine_delete_csrf']); ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-danger"
                                    >
                                        Delete
                                    </button>
                                </form>

                            <?php endif; ?>
                        </td>
                    </tr>

                <?php
                    }

                } else {
                    echo "<tr><td colspan='6'>No medicines found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("includes/footer.php"); ?>