<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

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

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$specialty = trim($_POST['specialty'] ?? '');
$password = $_POST['password'] ?? '';

if ($name === '' || $email === '' || $password === '') {
    http_response_code(400);
    echo 'Missing required fields';
    exit;
}

// Check if email exists
$check = $conn->prepare("SELECT docid FROM doctor WHERE email = ?");
$check->bind_param('s', $email);
$check->execute();
$cres = $check->get_result();
if ($cres->num_rows > 0) {
    http_response_code(409);
    echo 'Email already exists';
    exit;
}
$check->close();

// Insert doctor (store password as provided; recommend hashing later)
$stmt = $conn->prepare("INSERT INTO doctor (name, email, password, specialty) VALUES (?, ?, ?, ?)");
$stmt->bind_param('ssss', $name, $email, $password, $specialty);
$ok = $stmt->execute();
$stmt->close();

if ($ok) {
    header('Location: ../../pages/adminDashboard/admin_viewDoctors.php');
    exit;
} else {
    http_response_code(500);
    echo 'Failed to create doctor';
}

?>
