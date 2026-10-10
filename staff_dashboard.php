<?php
session_start();
include("config.php");
include("includes/header.php");
include("includes/staff_navbar.php");

if (!isset($_SESSION['name'])) {
    header("Location: login.php");
    exit();
}



$total_prescriptions_result = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM prescriptions
");
$total_prescriptions = mysqli_fetch_assoc($total_prescriptions_result)['total'];


$dispensed_today = 0;
$check_dispensing = mysqli_query($conn, "SHOW TABLES LIKE 'dispensing'");
if (mysqli_num_rows($check_dispensing) > 0) {
    $dispensed_today_result = mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM dispensing
        WHERE DATE(dispense_date) = CURDATE()
    ");

    if ($dispensed_today_result) {
        $dispensed_today = mysqli_fetch_assoc($dispensed_today_result)['total'];
    }
}


$stock_movements_query = "
    SELECT 
        'Dispensed' AS movement_type,
        m.name AS medicine_name,
        pi.quantity AS qty,
        d.dispense_date AS movement_date,
        CONCAT('Prescription #', d.prescription_id) AS note
    FROM dispensing d
    JOIN prescription_items pi ON d.prescription_id = pi.prescription_id
    JOIN medicines m ON pi.medicine_id = m.id
";


$check_adjustments = mysqli_query($conn, "SHOW TABLES LIKE 'stock_adjustments'");
if (mysqli_num_rows($check_adjustments) > 0) {

    $has_adjustment_type = mysqli_num_rows(mysqli_query($conn, "SHOW COLUMNS FROM stock_adjustments LIKE 'adjustment_type'")) > 0;
    $has_adjustment_date = mysqli_num_rows(mysqli_query($conn, "SHOW COLUMNS FROM stock_adjustments LIKE 'adjustment_date'")) > 0;
    $has_reason = mysqli_num_rows(mysqli_query($conn, "SHOW COLUMNS FROM stock_adjustments LIKE 'reason'")) > 0;
    $has_quantity = mysqli_num_rows(mysqli_query($conn, "SHOW COLUMNS FROM stock_adjustments LIKE 'quantity'")) > 0;
    $has_medicine_id = mysqli_num_rows(mysqli_query($conn, "SHOW COLUMNS FROM stock_adjustments LIKE 'medicine_id'")) > 0;

    if ($has_adjustment_type && $has_adjustment_date && $has_reason && $has_quantity && $has_medicine_id) {
        $stock_movements_query .= "
            UNION ALL
            SELECT
                CASE 
                    WHEN sa.adjustment_type = 'add' THEN 'Stock In'
                    ELSE 'Stock Out'
                END AS movement_type,
                m.name AS medicine_name,
                sa.quantity AS qty,
                sa.adjustment_date AS movement_date,
                sa.reason AS note
            FROM stock_adjustments sa
            JOIN medicines m ON sa.medicine_id = m.id
        ";
    }
}

$stock_movements_query .= "
    ORDER BY movement_date DESC
    LIMIT 10
";

$stock_movements_result = mysqli_query($conn, $stock_movements_query);


$low_stock_alert_result = mysqli_query($conn, "
    SELECT m.name, b.quantity
    FROM batches b
    JOIN medicines m ON b.medicine_id = m.id
    WHERE b.quantity < 10
    ORDER BY b.quantity ASC
    LIMIT 5
");
?>


<div class="main-content">
    <div class="container-fluid mt-3">
        <div class="row g-4">

            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h3 class="mb-2">Welcome back, <?php echo $_SESSION['name']; ?> 👋</h3>
                        <p class="text-muted mb-4">Here is your staff overview for today.</p>

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-uppercase text-muted">Total Prescriptions</h6>
                                <h3><?php echo $total_prescriptions; ?></h3>
                            </div>

                            <div class="col-md-6">
                                <h6 class="text-uppercase text-muted">Dispensed Today</h6>
                                <h3><?php echo $dispensed_today; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6 col-xl-3">
                        <a href="add_prescription.php" class="btn btn-primary w-100 p-3">+ Create Prescription</a>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <a href="prescriptions.php" class="btn btn-success w-100 p-3">View Prescriptions</a>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <a href="dispense.php" class="btn btn-warning w-100 p-3">Dispense Medicines</a>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <a href="payments.php" class="btn btn-info w-100 p-3">Payments</a>
                    </div>
                </div>

                <div class="card mt-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">Recent Stock Movements</h5>
    </div>

    <div class="card-body p-0">

        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">

            <table class="table table-bordered table-striped mb-0">

                <thead class="table-light sticky-top">
                    <tr>
                        <th>Type</th>
                        <th>Medicine</th>
                        <th>Qty</th>
                        <th>Date</th>
                        <th>Note</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if ($stock_movements_result && mysqli_num_rows($stock_movements_result) > 0) { ?>

                        <?php while ($move = mysqli_fetch_assoc($stock_movements_result)) { ?>

                            <tr>

                                <td>

                                    <?php

                                    if ($move['movement_type'] == 'Stock In') {
                                        echo "<span class='badge bg-success'>Stock In</span>";
                                    }

                                    elseif ($move['movement_type'] == 'Stock Out') {
                                        echo "<span class='badge bg-danger'>Stock Out</span>";
                                    }

                                    else {
                                        echo "<span class='badge bg-warning text-dark'>Dispensed</span>";
                                    }

                                    ?>

                                </td>

                                <td><?php echo $move['medicine_name']; ?></td>

                                <td><?php echo $move['qty']; ?></td>

                                <td><?php echo $move['movement_date']; ?></td>

                                <td><?php echo $move['note']; ?></td>

                            </tr>

                        <?php } ?>

                    <?php } else { ?>

                        <tr>
                            <td colspan="5">No stock movement activity found.</td>
                        </tr>

                    <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Staff Alerts</h5>
                    </div>

                    <div class="list-group list-group-flush">
                        <?php if (mysqli_num_rows($low_stock_alert_result) > 0) { ?>
                            <?php while ($alert = mysqli_fetch_assoc($low_stock_alert_result)) { ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><?php echo $alert['name']; ?></span>
                                    <span class="badge bg-warning text-dark">
                                        <?php echo $alert['quantity']; ?>
                                    </span>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="list-group-item">
                                No urgent stock alerts.
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>