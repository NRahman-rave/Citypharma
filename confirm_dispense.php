<?php

session_start();

include("config.php");


$pid = $_GET['pid'];

$user = $_SESSION['user_id'];

mysqli_query($conn, "

INSERT INTO dispensing

(prescription_id, user_id, dispense_date)

VALUES

('$pid','$user',NOW())

");

header("Location: staff_dashboard.php");

?>