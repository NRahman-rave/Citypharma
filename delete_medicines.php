<?php
session_start();
include("config.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

   
    $check_batches = mysqli_query($conn, "SELECT COUNT(*) AS total FROM batches WHERE medicine_id = '$id'");
    $batch_data = mysqli_fetch_assoc($check_batches);

    if ($batch_data['total'] > 0) {
        echo "
        <script>
            alert('Cannot delete this medicine because it has related batch records.');
            window.location.href='medicines.php';
        </script>
        ";
        exit();
    }

    
    $delete_query = "DELETE FROM medicines WHERE id = '$id'";

    if (mysqli_query($conn, $delete_query)) {
        echo "
        <script>
            alert('Medicine deleted successfully.');
            window.location.href='medicines.php';
        </script>
        ";
    } else {
        echo "Error deleting medicine: " . mysqli_error($conn);
    }
} else {
    header("Location: medicines.php");
    exit();
}
?>