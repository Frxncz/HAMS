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
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');

if (!$pid || $first_name === '' || $last_name === '' || $email === '') {
    http_response_code(400);
    echo 'Missing required fields';
    exit;
}

// validate email uniqueness (exclude current patient)
$stmt = $conn->prepare("SELECT pid FROM patient WHERE email = ? AND pid != ? LIMIT 1");
$stmt->bind_param('si', $email, $pid);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    http_response_code(409);
    echo 'Email already used by another patient';
    exit;
}
$stmt->close();

$stmt = $conn->prepare("UPDATE patient SET first_name = ?, last_name = ?, email = ? WHERE pid = ?");
if (!$stmt) {
    http_response_code(500);
    echo 'DB prepare error: ' . $conn->error;
    exit;
}
$stmt->bind_param('sssi', $first_name, $last_name, $email, $pid);
$ok = $stmt->execute();
$err = $stmt->error;
$stmt->close();

if ($ok) {
    // If update was done via AJAX, return JSON
    $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
          (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'pid' => $pid]);
        exit;
    }
    header('Location: ../../pages/adminDashboard/admin_viewPatients.php');
    exit;
} else {
    http_response_code(500);
    echo 'Failed to update: ' . ($err ?: $conn->error);
    exit;
}

?>