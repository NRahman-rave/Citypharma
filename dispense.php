<?php
session_start();
include("config.php");
include("includes/header.php");
include("includes/staff_navbar.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$prescription_id = isset($_GET['prescription_id']) ? $_GET['prescription_id'] : null;
$message = "";
$prescription = null;

if ($prescription_id) {
    $prescription_query = "
        SELECT p.*, u.name AS staff_name
        FROM prescriptions p
        LEFT JOIN users u ON p.created_by = u.id
        WHERE p.id = '$prescription_id'
    ";
    $prescription_result = mysqli_query($conn, $prescription_query);
    $prescription = mysqli_fetch_assoc($prescription_result);

    if (!$prescription) {
        $message = "<div class='alert alert-danger'>Prescription not found.</div>";
        $prescription_id = null;
    }
}

if ($prescription_id) {
    $items_query = "
        SELECT 
            pi.medicine_id,
            pi.quantity,
            m.name AS medicine_name,
            m.selling_price
        FROM prescription_items pi
        JOIN medicines m ON pi.medicine_id = m.id
        WHERE pi.prescription_id = '$prescription_id'
    ";
}

if ($prescription_id && isset($_POST['dispense'])) {

    $items_check_result = mysqli_query($conn, $items_query);
    $stock_ok = true;

    while ($item = mysqli_fetch_assoc($items_check_result)) {
        $medicine_id = $item['medicine_id'];
        $qty_needed = $item['quantity'];

        $stock_query = "
            SELECT IFNULL(SUM(quantity), 0) AS total_stock
            FROM batches
            WHERE medicine_id = '$medicine_id'
              AND expiry_date >= CURDATE()
        ";
        $stock_result = mysqli_query($conn, $stock_query);
        $stock = mysqli_fetch_assoc($stock_result);
        $available_stock = $stock['total_stock'];

        if ($available_stock < $qty_needed) {
            $message .= "<div class='alert alert-danger'>
                Not enough stock for <b>" . $item['medicine_name'] . "</b>
                (Needed: $qty_needed, Available: $available_stock)
            </div>";
            $stock_ok = false;
        }
    }

    if ($stock_ok) {
        $items_result = mysqli_query($conn, $items_query);
        $total_sale = 0;

        while ($item = mysqli_fetch_assoc($items_result)) {
            $medicine_id = $item['medicine_id'];
            $qty_needed = $item['quantity'];

            $line_total = $item['selling_price'] * $item['quantity'];
            $total_sale += $line_total;

            $batch_query = "
                SELECT *
                FROM batches
                WHERE medicine_id = '$medicine_id'
                  AND quantity > 0
                  AND expiry_date >= CURDATE()
                ORDER BY expiry_date ASC
            ";
            $batch_result = mysqli_query($conn, $batch_query);

            while (($batch = mysqli_fetch_assoc($batch_result)) && $qty_needed > 0) {
                $batch_id = $batch['id'];
                $available = $batch['quantity'];

                if ($available >= $qty_needed) {
                    mysqli_query($conn, "
                        UPDATE batches
                        SET quantity = quantity - $qty_needed
                        WHERE id = '$batch_id'
                    ");
                    $qty_needed = 0;
                } else {
                    mysqli_query($conn, "
                        UPDATE batches
                        SET quantity = 0
                        WHERE id = '$batch_id'
                    ");
                    $qty_needed -= $available;
                }
            }
        }

        $insert_dispensing = mysqli_query($conn, "
            INSERT INTO dispensing
            (prescription_id, user_id, dispense_date)
            VALUES
            ('$prescription_id', '" . $_SESSION['user_id'] . "', NOW())
        ");

        if ($insert_dispensing) {
            mysqli_query($conn, "
                INSERT INTO sales
                (total_amount, sale_date)
                VALUES
                ('$total_sale', NOW())
            ");

            mysqli_query($conn, "
INSERT INTO audit_log (user_id, action, table_name, record_id, date)
VALUES ('".$_SESSION['user_id']."', 'DISPENSE', 'prescriptions', '$prescription_id', NOW())
");

            header("Location: payment.php?prescription_id=" . $prescription_id);
            exit();
        } else {
            $message = "<div class='alert alert-danger'>
                Error saving dispensing record: " . mysqli_error($conn) . "
            </div>";
        }
    }
}
?>

<div class="main-content">
    <div class="container mt-4">

        <h2>Dispense Medicine</h2>

        <?php echo $message; ?>

        <form method="GET" class="mb-4">
            <div class="mb-3">
                <label>Enter Prescription ID</label>
                <input type="number" name="prescription_id" class="form-control" required>
            </div>
            <button class="btn btn-primary">Load Prescription</button>
        </form>

        <?php if ($prescription_id && $prescription) { ?>

            <div class="card mb-3">
                <div class="card-body">
                    <p><strong>Patient:</strong> <?php echo $prescription['patient_name']; ?></p>
                    <p><strong>Doctor:</strong> <?php echo $prescription['doctor_name']; ?></p>
                    <p><strong>Date Created:</strong> <?php echo $prescription['date_created']; ?></p>
                    <p><strong>Created By:</strong> <?php echo $prescription['staff_name'] ? $prescription['staff_name'] : '-'; ?></p>
                </div>
            </div>

            <h4>Medicines</h4>

            <table class="table table-bordered">
                <tr>
                    <th>Medicine</th>
                    <th>Quantity Needed</th>
                    <th>Available Stock</th>
                    <th>Status</th>
                </tr>

                <?php
                $items_display_result = mysqli_query($conn, $items_query);

                while ($row = mysqli_fetch_assoc($items_display_result)) {
                    $medicine_id = $row['medicine_id'];

                    $stock_result = mysqli_query($conn, "
                        SELECT IFNULL(SUM(quantity), 0) AS total_stock
                        FROM batches
                        WHERE medicine_id = '$medicine_id'
                          AND expiry_date >= CURDATE()
                    ");
                    $stock = mysqli_fetch_assoc($stock_result);
                    $available_stock = $stock['total_stock'];

                    $status = ($available_stock >= $row['quantity'])
                        ? "<span class='badge bg-success'>Enough Stock</span>"
                        : "<span class='badge bg-danger'>Insufficient Stock</span>";
                ?>
                    <tr>
                        <td><?php echo $row['medicine_name']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td><?php echo $available_stock; ?></td>
                        <td><?php echo $status; ?></td>
                    </tr>
                <?php } ?>
            </table>

            <form method="POST">
                <button class="btn btn-success" name="dispense">Confirm Dispense</button>
                <a href="prescriptions.php" class="btn btn-secondary">Back</a>
            </form>

        <?php } ?>

    </div>
</div>

<?php include("includes/footer.php"); ?>