<?php

session_start();
require_once 'config.php';

if (isset($_POST['login'])) {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email !== '' && $password !== '') {

        // Find the user securely.
        $stmt = $conn->prepare(
            "SELECT id, name, email, password, role
             FROM users
             WHERE email = ?
             LIMIT 1"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $stmt->close();

        // Verify the submitted password against its stored hash.
        if ($user && password_verify($password, $user['password'])) {

            // Prevent session fixation attacks.
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Redirect users according to their roles.
            if ($user['role'] === 'manager') {

                header("Location: manager_dashboard.php");
                exit();

            } elseif ($user['role'] === 'staff') {

                header("Location: staff_dashboard.php");
                exit();
            }

            // Reject accounts with unrecognised roles.
            $_SESSION = [];
            session_destroy();
        }
    }

    // Generic error prevents revealing whether an email exists.
    $_SESSION['login_error'] = "Incorrect email or password";
    $_SESSION['active_form'] = "login";

    header("Location: login.php");
    exit();
}
?>