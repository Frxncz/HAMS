<?php
session_start();
include('../db_connect.php'); // ✅ fixed include path

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'patient') {
  die("Unauthorized access.");
}

$patient_id = $_SESSION['user_id'];
$id = intval($_POST['id']);
$appt_date = $_POST['appt_date'] ?? '';
$appt_time = $_POST['appt_time'] ?? '';
$purpose = $_POST['purpose'] ?? '';

if (empty($appt_date) || empty($appt_time) || empty($purpose)) {
  $_SESSION['appt_error'] = "All fields are required!";
  header("Location: ../../pages/patientDashboard/patient_myConsultation.php");
  exit();
}

$stmt = $conn->prepare("UPDATE appointments 
                        SET appt_date = ?, appt_time = ?, purpose = ? 
                        WHERE id = ? AND patient_id = ?");
$stmt->bind_param("sssii", $appt_date, $appt_time, $purpose, $id, $patient_id);

if ($stmt->execute()) {
  $_SESSION['appt_success'] = "Appointment updated successfully.";
} else {
  $_SESSION['appt_error'] = "Error updating appointment: " . $stmt->error;
}

$stmt->close();
$conn->close();

header("Location: ../../pages/patientDashboard/patient_myConsultation.php");
exit();
?>
