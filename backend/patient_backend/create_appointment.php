<?php
session_start();
include('../db_connect.php');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: ../login.html");
    exit();
}

$patient_id = $_SESSION['user_id'] ?? null;
$doctor_id = $_POST['doctor_id'] ?? null;
$appt_date = $_POST['appt_date'] ?? null;
$appt_time = $_POST['appt_time'] ?? null;
$purpose = $_POST['purpose'] ?? null;

// Check for empty values, not just null
if (empty($patient_id) || empty($doctor_id) || empty($appt_date) || empty($appt_time) || empty($purpose)) {
    // Debug: Show which field is missing
    $missing = [];
    if (empty($patient_id)) $missing[] = 'patient_id';
    if (empty($doctor_id)) $missing[] = 'doctor_id';
    if (empty($appt_date)) $missing[] = 'appt_date';
    if (empty($appt_time)) $missing[] = 'appt_time';
    if (empty($purpose)) $missing[] = 'purpose';
    
    $_SESSION['appt_error'] = "All fields are required. Missing: " . implode(', ', $missing);
    header("Location: ../../pages/patientDashboard/patient_dashboard.php");
    exit();
}

// Insert appointment
$stmt = $conn->prepare("
    INSERT INTO appointments (patient_id, doctor_id, appt_date, appt_time, purpose, status)
    VALUES (?, ?, ?, ?, ?, 'pending')
");
$stmt->bind_param("iisss", $patient_id, $doctor_id, $appt_date, $appt_time, $purpose);

if ($stmt->execute()) {
    $_SESSION['appt_success'] = "Appointment scheduled successfully!";
} else {
    $_SESSION['appt_error'] = "Error scheduling appointment: " . $stmt->error;
}

$stmt->close();
$conn->close();

header("Location: ../../pages/patientDashboard/patient_dashboard.php");
exit();
?>