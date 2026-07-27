<?php
include("config.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $delete_query = "DELETE FROM batches WHERE id = '$id'";

    if (mysqli_query($conn, $delete_query)) {
        echo "
        <script>
            alert('Batch deleted successfully.');
            window.location.href='manage_batches.php';
        </script>
        ";
    } else {
        echo "Error deleting batch: " . mysqli_error($conn);
    }
} else {
    header("Location: manage_batches.php");
    exit();
}
?>