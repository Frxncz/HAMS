<?php
session_start();

// ensure patient is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient') {
    http_response_code(401);
    die("Unauthorized");
}

// include DB connection from the Models layer
require_once __DIR__ . '/../../Models/db_connect.php'; 

// make sure $conn exists
if (!isset($conn) || !$conn instanceof mysqli) {
    error_log("DB connection missing or invalid in create_appointment.php");
    die("Server error: database connection not available.");
}

// get and validate POST inputs
$patient_id = intval($_SESSION['user_id']);
$doctor_id  = isset($_POST['doctor_id']) ? intval($_POST['doctor_id']) : 0;
$appt_date  = $_POST['appt_date'] ?? '';
$appt_time  = $_POST['appt_time'] ?? '';
$purpose    = $_POST['purpose'] ?? '';

if ($doctor_id <= 0 || empty($appt_date) || empty($appt_time) || empty($purpose)) {
    $_SESSION['appt_error'] = "Please fill in all appointment fields.";
    header("Location: /HAMS/public/patient/patient_dashboard.php");
    exit();
}

// optional: validate purpose is one of allowed values
$allowed = ['regular', 'new_patient', 'follow_up'];
if (!in_array($purpose, $allowed, true)) {
    $_SESSION['appt_error'] = "Invalid purpose selected.";
    header("Location: /HAMS/public/patient/patient_dashboard.php");
    exit();
}

// Insert appointment
$stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appt_date, appt_time, purpose, status) VALUES (?, ?, ?, ?, ?, 'pending')");
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    $_SESSION['appt_error'] = "Server error (prepare).";
    header("Location: /HAMS/public/patient/patient_dashboard.php");
    exit();
}

$stmt->bind_param("iisss", $patient_id, $doctor_id, $appt_date, $appt_time, $purpose);

if ($stmt->execute()) {
    $_SESSION['appt_success'] = "Appointment scheduled successfully.";
    $stmt->close();
    header("Location: /HAMS/public/patient/patient_myConsultation.php");
    exit();
} else {
    error_log("Execute failed: " . $stmt->error);
    $_SESSION['appt_error'] = "Server error (execute).";
    $stmt->close();
    header("Location: /HAMS/public/patient/patient_dashboard.php");
    exit();
}

