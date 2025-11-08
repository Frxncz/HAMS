<?php
session_start();

// only doctors allowed
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'doctor') {
    header('Location: ../../login.html');
    exit();
}

require_once __DIR__ . '/../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['msg_error'] = 'Invalid request method.';
    header('Location: ../../pages/doctorDashboard/doctor_myAppointments.php');
    exit();
}

$appointment_id = isset($_POST['appointment_id']) ? intval($_POST['appointment_id']) : 0;
$action = $_POST['action'] ?? '';

if ($appointment_id <= 0 || !in_array($action, ['accept', 'decline'], true)) {
    $_SESSION['msg_error'] = 'Invalid parameters.';
    header('Location: ../../pages/doctorDashboard/doctor_myAppointments.php');
    exit();
}

$doctor_id = intval($_SESSION['user_id']);

// verify appointment belongs to this doctor
$check = $conn->prepare('SELECT id, status FROM appointments WHERE id = ? AND doctor_id = ?');
if (!$check) {
    error_log('Prepare failed: ' . $conn->error);
    $_SESSION['msg_error'] = 'Server error.';
    header('Location: ../../pages/doctorDashboard/doctor_myAppointments.php');
    exit();
}
$check->bind_param('ii', $appointment_id, $doctor_id);
$check->execute();
$res = $check->get_result();
if ($res->num_rows === 0) {
    $_SESSION['msg_error'] = 'Appointment not found or unauthorized.';
    $check->close();
    header('Location: ../../pages/doctorDashboard/doctor_myAppointments.php');
    exit();
}
$row = $res->fetch_assoc();
$current_status = $row['status'];
$check->close();

// map action to status
$new_status = $action === 'accept' ? 'confirmed' : 'declined';

// no-op if already the same
if ($current_status === $new_status) {
    $_SESSION['msg_success'] = 'No change required.';
    header('Location: ../../pages/doctorDashboard/doctor_myAppointments.php');
    exit();
}

$upd = $conn->prepare('UPDATE appointments SET status = ? WHERE id = ? AND doctor_id = ?');
if (!$upd) {
    error_log('Prepare failed: ' . $conn->error);
    $_SESSION['msg_error'] = 'Server error.';
    header('Location: ../../pages/doctorDashboard/doctor_myAppointments.php');
    exit();
}
$upd->bind_param('sii', $new_status, $appointment_id, $doctor_id);
if ($upd->execute()) {
    $_SESSION['msg_success'] = 'Appointment ' . ($action === 'accept' ? 'confirmed' : 'declined') . ' successfully.';
} else {
    error_log('Execute failed: ' . $upd->error);
    $_SESSION['msg_error'] = 'Failed to update appointment.';
}
$upd->close();

header('Location: ../../pages/doctorDashboard/doctor_myAppointments.php');
exit();
