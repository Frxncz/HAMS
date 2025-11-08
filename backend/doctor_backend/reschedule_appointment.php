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
    header('Location: ../../pages/doctorDashboard/doctor_mySessions.php');
    exit();
}

$appointment_id = isset($_POST['appointment_id']) ? intval($_POST['appointment_id']) : 0;
$new_date = $_POST['new_date'] ?? '';
$new_time = $_POST['new_time'] ?? '';

if ($appointment_id <= 0 || empty($new_date) || empty($new_time)) {
    $_SESSION['msg_error'] = 'Please provide a valid date and time.';
    header('Location: ../../pages/doctorDashboard/doctor_mySessions.php');
    exit();
}

$doctor_id = intval($_SESSION['user_id']);

// verify appointment belongs to this doctor
$check = $conn->prepare('SELECT id FROM appointments WHERE id = ? AND doctor_id = ?');
if (!$check) {
    error_log('Prepare failed: ' . $conn->error);
    $_SESSION['msg_error'] = 'Server error.';
    header('Location: ../../pages/doctorDashboard/doctor_mySessions.php');
    exit();
}
$check->bind_param('ii', $appointment_id, $doctor_id);
$check->execute();
$res = $check->get_result();
if ($res->num_rows === 0) {
    $_SESSION['msg_error'] = 'Appointment not found or unauthorized.';
    $check->close();
    header('Location: ../../pages/doctorDashboard/doctor_mySessions.php');
    exit();
}
$check->close();

// update appointment date/time
$upd = $conn->prepare('UPDATE appointments SET appt_date = ?, appt_time = ? WHERE id = ? AND doctor_id = ?');
if (!$upd) {
    error_log('Prepare failed: ' . $conn->error);
    $_SESSION['msg_error'] = 'Server error.';
    header('Location: ../../pages/doctorDashboard/doctor_mySessions.php');
    exit();
}
$upd->bind_param('ssii', $new_date, $new_time, $appointment_id, $doctor_id);
if ($upd->execute()) {
    $_SESSION['msg_success'] = 'Appointment rescheduled successfully.';
} else {
    error_log('Execute failed: ' . $upd->error);
    $_SESSION['msg_error'] = 'Failed to reschedule appointment.';
}
$upd->close();

header('Location: ../../pages/doctorDashboard/doctor_mySessions.php');
exit();
