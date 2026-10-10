<?php
include 'config.php';
$result = mysqli_query($conn, "SELECT * FROM medicines ORDER BY medicine_id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Medicines</title>
    <link rel="stylesheet" href="manage_medicines.css">
</head>
<body>
<h2>Medicine List</h2>
<a href="add_medicine.php">Add New Medicine</a><br><br>
<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Category</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Expiry Date</th>
        <th>Supplier</th>
        <th>Actions</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?= $row['medicine_id']; ?></td>
        <td><?= $row['medicine_name']; ?></td>
        <td><?= $row['category']; ?></td>
        <td><?= $row['price']; ?></td>
        <td><?= $row['stock']; ?></td>
        <td><?= $row['expiry_date']; ?></td>
        <td><?= $row['supplier']; ?></td>
        <td>
            <a href="edit_medicine.php?id=<?= $row['medicine_id']; ?>">Edit</a> | 
            <a href="delete_medicine.php?id=<?= $row['medicine_id']; ?>" 
               onclick="return confirm('Are you sure?')">Delete</a>
        </td>
    </tr>
    <?php } ?>
</table>
</body>
</html>