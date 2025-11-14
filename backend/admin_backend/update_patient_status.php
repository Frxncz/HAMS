<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

// Admin only
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method not allowed';
    exit;
}

$pid = isset($_POST['pid']) ? intval($_POST['pid']) : 0;
$action = $_POST['action'] ?? '';

if (!$pid || !in_array($action, ['activate','deactivate'])) {
    http_response_code(400);
    echo 'Invalid input';
    exit;
}

$status = $action === 'activate' ? 'active' : 'inactive';

// detect AJAX
$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
          (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

// Ensure status column exists on patient table
$check = $conn->query("SHOW COLUMNS FROM patient LIKE 'status'");
if (!($check && $check->num_rows > 0)) {
    if (!$conn->query("ALTER TABLE patient ADD COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'active'")) {
        $err = $conn->error ?: 'Failed to add status column';
        if ($isAjax) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to add status column: ' . $err]);
            exit;
        } else {
            http_response_code(500);
            echo 'Failed to add status column: ' . $err;
            exit;
        }
    }
}

$stmt = $conn->prepare("UPDATE patient SET status = ? WHERE pid = ?");
if (!$stmt) {
    $err = $conn->error ?: 'DB prepare error';
    if ($isAjax) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $err]);
        exit;
    }
    http_response_code(500);
    echo 'DB error: ' . $err;
    exit;
}
$stmt->bind_param('si', $status, $pid);
$ok = $stmt->execute();
$stmtErr = $stmt->error;
$stmt->close();

if ($ok) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'status' => $status, 'pid' => $pid]);
        exit;
    } else {
        header('Location: ../../pages/adminDashboard/admin_viewPatients.php');
        exit;
    }
} else {
    if ($isAjax) {
        header('Content-Type: application/json');
        http_response_code(500);
        $errMsg = $stmtErr ?: ($conn->error ?: 'Failed to update');
        echo json_encode(['success' => false, 'error' => $errMsg]);
        exit;
    } else {
        http_response_code(500);
        $errMsg = $stmtErr ?: ($conn->error ?: 'Failed to update');
        echo 'Failed to update: ' . $errMsg;
        exit;
    }
}

?>