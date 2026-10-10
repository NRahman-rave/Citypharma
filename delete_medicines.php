<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/config.php";

// Only managers can delete medicines.
if (
    !isset($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'manager'
) {
    http_response_code(403);
    exit("Access denied.");
}

// Reject GET requests.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header("Allow: POST");
    exit("Method not allowed.");
}

// Verify CSRF token.
$submittedToken = $_POST['csrf_token'] ?? '';

if (
    !is_string($submittedToken) ||
    empty($_SESSION['medicine_delete_csrf']) ||
    !hash_equals($_SESSION['medicine_delete_csrf'], $submittedToken)
) {
    http_response_code(403);
    exit("Invalid security token. Refresh and try again.");
}

// Validate medicine ID.
$id = filter_input(
    INPUT_POST,
    'id',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if (!$id) {
    header("Location: medicines.php?delete_status=invalid");
    exit();
}

$status = 'error';

try {
    $conn->begin_transaction();

    // Lock the medicine record during deletion.
    $stmt = $conn->prepare(
        "SELECT id FROM medicines WHERE id = ? FOR UPDATE"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $medicine = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$medicine) {

        $conn->rollback();
        $status = 'not_found';

    } else {

        // Preserve existing batch protection.
        $stmt = $conn->prepare(
            "SELECT COUNT(*) FROM batches WHERE medicine_id = ?"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($batchCount);
        $stmt->fetch();
        $stmt->close();

        if ((int) $batchCount > 0) {

            $conn->rollback();
            $status = 'blocked';

        } else {

            // Delete using a prepared statement.
            $stmt = $conn->prepare(
                "DELETE FROM medicines WHERE id = ?"
            );

            $stmt->bind_param("i", $id);
            $stmt->execute();

            $deleted = $stmt->affected_rows === 1;
            $stmt->close();

            if ($deleted) {
                $conn->commit();
                $status = 'deleted';
            } else {
                $conn->rollback();
                $status = 'not_found';
            }
        }
    }

} catch (Throwable $e) {

    $conn->rollback();

    // Record technical errors privately.
    error_log("Medicine deletion failed: " . $e->getMessage());

    $status = 'error';
}

// Redirect safely after processing.
header("Location: medicines.php?delete_status=" . $status);
exit();
?>