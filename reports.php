<?php
include("config.php");
include("includes/header.php");
include("includes/navbar.php");


$daily_sales_result = mysqli_query($conn, "
    SELECT DATE(sale_date) AS sales_day,
           COUNT(*) AS total_transactions,
           IFNULL(SUM(total_amount),0) AS total_sales
    FROM sales
    GROUP BY DATE(sale_date)
    ORDER BY DATE(sale_date) DESC
");


$monthly_sales_result = mysqli_query($conn, "
    SELECT DATE_FORMAT(sale_date, '%Y-%m') AS sales_month,
           COUNT(*) AS total_transactions,
           IFNULL(SUM(total_amount),0) AS total_sales
    FROM sales
    GROUP BY DATE_FORMAT(sale_date, '%Y-%m')
    ORDER BY sales_month DESC
");


$expired_report_result = mysqli_query($conn, "
    SELECT m.name, b.batch_number, b.quantity, b.expiry_date
    FROM batches b
    JOIN medicines m ON b.medicine_id = m.id
    WHERE b.expiry_date < CURDATE()
    ORDER BY b.expiry_date ASC
");


$has_price_column = mysqli_query($conn, "SHOW COLUMNS FROM medicines LIKE 'price'");
$use_price = mysqli_num_rows($has_price_column) > 0;

if ($use_price) {
    $stock_valuation_result = mysqli_query($conn, "
        SELECT m.name,
               SUM(b.quantity) AS total_quantity,
               m.price,
               (SUM(b.quantity) * m.price) AS stock_value
        FROM batches b
        JOIN medicines m ON b.medicine_id = m.id
        GROUP BY m.id, m.name, m.price
        ORDER BY m.name ASC
    ");
} else {
    $stock_valuation_result = mysqli_query($conn, "
        SELECT m.name,
               SUM(b.quantity) AS total_quantity
        FROM batches b
        JOIN medicines m ON b.medicine_id = m.id
        GROUP BY m.id, m.name
        ORDER BY m.name ASC
    ");
}
?>

<div class="main-content">
    <div class="container mt-4">
        <h2 class="mb-4">Reports</h2>

        <h4>Daily Sales Report</h4>
        <table class="table table-bordered table-striped mb-5">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Total Transactions</th>
                    <th>Total Sales (£)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($daily_sales_result) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($daily_sales_result)) { ?>
                        <tr>
                            <td><?php echo $row['sales_day']; ?></td>
                            <td><?php echo $row['total_transactions']; ?></td>
                            <td>£<?php echo number_format($row['total_sales'], 2); ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr><td colspan="3">No daily sales found</td></tr>
                <?php } ?>
            </tbody>
        </table>

        <h4>Monthly Sales Report</h4>
        <table class="table table-bordered table-striped mb-5">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Total Transactions</th>
                    <th>Total Sales (£)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($monthly_sales_result) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($monthly_sales_result)) { ?>
                        <tr>
                            <td><?php echo $row['sales_month']; ?></td>
                            <td><?php echo $row['total_transactions']; ?></td>
                            <td>£<?php echo number_format($row['total_sales'], 2); ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr><td colspan="3">No monthly sales found</td></tr>
                <?php } ?>
            </tbody>
        </table>

        <h4>Expired Medicines Report</h4>
        <table class="table table-bordered table-striped mb-5">
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>Batch Number</th>
                    <th>Quantity</th>
                    <th>Expiry Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($expired_report_result) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($expired_report_result)) { ?>
                        <tr>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['batch_number']; ?></td>
                            <td><?php echo $row['quantity']; ?></td>
                            <td><?php echo $row['expiry_date']; ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr><td colspan="4">No expired medicines found</td></tr>
                <?php } ?>
            </tbody>
        </table>

        <h4>Stock Valuation Report</h4>
        <table class="table table-bordered table-striped mb-4">
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>Total Quantity</th>
                    <?php if ($use_price) { ?>
                        <th>Unit Price (£)</th>
                        <th>Stock Value (£)</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($stock_valuation_result) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($stock_valuation_result)) { ?>
                        <tr>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['total_quantity']; ?></td>
                            <?php if ($use_price) { ?>
                                <td>£<?php echo number_format($row['price'], 2); ?></td>
                                <td>£<?php echo number_format($row['stock_value'], 2); ?></td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="<?php echo $use_price ? 4 : 2; ?>">No stock data found</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("includes/footer.php"); ?>