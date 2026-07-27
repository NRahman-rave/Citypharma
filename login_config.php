<?php
session_start();
require_once 'config.php';

if (isset($_POST['login'])) {

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {

$user = $result->fetch_assoc();

if ($password == $user['password']) {

$_SESSION['user_id'] = $user['id'];   
$_SESSION['name'] = $user['name'];
$_SESSION['email'] = $user['email'];
$_SESSION['role'] = $user['role'];

if ($user['role'] === 'manager') {
header("Location: manager_dashboard.php");
} else {
header("Location: staff_dashboard.php");
}

exit();

}

}

$_SESSION['login_error'] = "Incorrect email or password";
$_SESSION['active_form'] = "login";
header("Location: login.php");
exit();

}
?>