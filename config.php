<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "citypharma_db"
);

if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

?>