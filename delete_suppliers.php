<?php
include("config.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

   
    $check = mysqli_query($conn, "SELECT COUNT(*) AS total FROM medicines WHERE supplier_id = '$id'");
    $data = mysqli_fetch_assoc($check);

    if ($data['total'] > 0) {
        echo "
        <script>
            alert('Cannot delete this supplier because it is linked to existing medicines.');
            window.location.href='suppliers.php';
        </script>
        ";
        exit();
    }

    $delete_query = "DELETE FROM suppliers WHERE id = '$id'";

    if (mysqli_query($conn, $delete_query)) {
        echo "
        <script>
            alert('Supplier deleted successfully.');
            window.location.href='suppliers.php';
        </script>
        ";
    } else {
        echo "Error deleting supplier: " . mysqli_error($conn);
    }
} else {
    header("Location: suppliers.php");
    exit();
}
?>