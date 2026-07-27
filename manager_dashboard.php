<?php
include("config.php");
include("includes/header.php");
include("includes/navbar.php");

if (!isset($_SESSION['name'])) {
    header("Location: login.php");
    exit();   
}

$total_medicines_result = mysqli_query($conn,"
SELECT COUNT(*) AS total FROM medicines
");
$total_medicines = mysqli_fetch_assoc($total_medicines_result)['total'];



$total_suppliers_result = mysqli_query($conn,"
SELECT COUNT(*) AS total FROM suppliers
");
$total_suppliers = mysqli_fetch_assoc($total_suppliers_result)['total'];



$low_stock_result = mysqli_query($conn,"
SELECT COUNT(*) AS total
FROM batches
WHERE quantity < 10
");
$low_stock_count = mysqli_fetch_assoc($low_stock_result)['total'];



$expired_result = mysqli_query($conn,"
SELECT COUNT(*) AS total
FROM batches
WHERE expiry_date < CURDATE()
");
$expired_count = mysqli_fetch_assoc($expired_result)['total'];



$near_expiry_result = mysqli_query($conn,"
SELECT COUNT(*) AS total
FROM batches
WHERE expiry_date BETWEEN CURDATE()
AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
");
$near_expiry_count = mysqli_fetch_assoc($near_expiry_result)['total'];



$today_sales_result = mysqli_query($conn,"
SELECT IFNULL(SUM(total_amount),0) AS total
FROM sales
WHERE DATE(sale_date)=CURDATE()
");
$today_sales = mysqli_fetch_assoc($today_sales_result)['total'];



$avg_sales_result = mysqli_query($conn,"
SELECT IFNULL(AVG(total_amount),0) AS avg_sale
FROM sales
");
$avg_sales = mysqli_fetch_assoc($avg_sales_result)['avg_sale'];


$low_stock_query = "
    SELECT m.name, b.batch_number, b.quantity
    FROM batches b
    JOIN medicines m ON b.medicine_id = m.id
    WHERE b.quantity < 10
    ORDER BY b.quantity ASC
";

$low_stock_table_result = mysqli_query($conn, $low_stock_query);




$todo_tasks = [];

if($expired_count > 0){
    $todo_tasks[] = [
        "title"=>"Expired medicines need review",
        "count"=>$expired_count,
        "type"=>"danger"
    ];
}

if($near_expiry_count > 0){
    $todo_tasks[] = [
        "title"=>"Near expiry medicines",
        "count"=>$near_expiry_count,
        "type"=>"warning"
    ];
}

if($low_stock_count > 0){
    $todo_tasks[] = [
        "title"=>"Low stock medicines",
        "count"=>$low_stock_count,
        "type"=>"info"
    ];
}

if($total_suppliers == 0){
    $todo_tasks[] = [
        "title"=>"Add supplier",
        "count"=>1,
        "type"=>"secondary"
    ];
}

$chart_labels = [];
$chart_values = [];

$chart_result = mysqli_query($conn, "
    SELECT DATE(sale_date) AS sales_day,
           IFNULL(SUM(total_amount),0) AS total_sales
    FROM sales
    WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
    GROUP BY DATE(sale_date)
    ORDER BY DATE(sale_date) ASC
");

while ($row = mysqli_fetch_assoc($chart_result)) {
    $chart_labels[] = $row['sales_day'];
    $chart_values[] = $row['total_sales'];
}
?>
<link rel="stylesheet" href="manager.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<div class="main-content">
    <div class="container-fluid mt-3">

        <div class="row g-4">

            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h3 class="mb-2">Welcome back, <?php echo $_SESSION['name']; ?> 👋</h3>
                        <p class="text-muted mb-4">Here is today's pharmacy performance.</p>

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-uppercase text-muted">Today's Sales</h6>
                                <h3>£<?php echo number_format($today_sales, 2); ?></h3>
                            </div>

                            <div class="col-md-6">
                                <h6 class="text-uppercase text-muted">Average Sale</h6>
                                <h3>£<?php echo number_format($avg_sales, 2); ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h4 class="mb-3">Sales Last 7 Days</h4>
                        <canvas id="salesChart" height="100"></canvas>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6 col-xl-3">
                        <div class="card bg-primary text-white shadow-sm h-100">
                            <div class="card-body text-center">
                                <h2><?php echo $total_medicines; ?></h2>
                                <p class="mb-0">Total Medicines</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card bg-success text-white shadow-sm h-100">
                            <div class="card-body text-center">
                                <h2><?php echo $total_suppliers; ?></h2>
                                <p class="mb-0">Total Suppliers</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card bg-warning text-dark shadow-sm h-100">
                            <div class="card-body text-center">
                                <h2><?php echo $low_stock_count; ?></h2>
                                <p class="mb-0">Low Stock</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card bg-danger text-white shadow-sm h-100">
                            <div class="card-body text-center">
                                <h2><?php echo $expired_count; ?></h2>
                                <p class="mb-0">Expired Medicines</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Manager To-Do</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php if (!empty($todo_tasks)) { ?>
                            <?php foreach ($todo_tasks as $task) { ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><?php echo $task['title']; ?></span>
                                    <span class="badge bg-<?php echo $task['type']; ?>">
                                        <?php echo $task['count']; ?>
                                    </span>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="list-group-item">
                                No urgent tasks right now.
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

        </div>


    <h4>Low Stock Medicines</h4>

<table>
    <tr>
        <th>Medicine Name</th>
        <th>Batch Number</th>
        <th>Quantity (cases)</th>
    </tr>

    <?php if (mysqli_num_rows($low_stock_table_result) > 0) { ?>
    <?php while ($row = mysqli_fetch_assoc($low_stock_table_result)) { ?>
        <tr>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['batch_number']; ?></td>
            <td><?php echo $row['quantity']; ?></td>
        </tr>
    <?php } ?>
<?php } else { ?>
    <tr>
        <td colspan="3">No low stock medicines found</td>
    </tr>
<?php } ?>
</table>

    <hr>

    <h4>Expired Medicines</h4>
    <h2>Near Expiry Medicines</h2>

<table>
<tr>
    <th>Medicine Name</th>
    <th>Batch Number</th>
    <th>Expiry Date</th>
</tr>

<?php
$today = date('Y-m-d');
$limit = date('Y-m-d', strtotime('+30 days'));

$query = "
SELECT m.name, b.batch_number, b.expiry_date
FROM batches b
JOIN medicines m ON b.medicine_id = m.id
WHERE b.expiry_date BETWEEN '$today' AND '$limit'
";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
?>
        <tr>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['batch_number']; ?></td>
            <td><?php echo $row['expiry_date']; ?></td>
        </tr>
<?php
    }
} else {
?>
    <tr>
        <td colspan="3">No records found</td>
    </tr>
<?php
}
?>
</table>

    

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const chartCanvas = document.getElementById('salesChart');

    if (chartCanvas) {
        new Chart(chartCanvas, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    label: 'Sales (£)',
                    data: <?php echo json_encode($chart_values); ?>,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});
</script>

<?php include("includes/footer.php"); ?>

