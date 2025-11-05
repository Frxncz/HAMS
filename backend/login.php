<?php
include('db_connect.php');

$email = $_POST['email'];
$password = $_POST['password'];

if (empty($email) || empty($password)) {
    die("Please fill all fields.");
}

// 1. Check Admin
$admin = $conn->prepare("SELECT * FROM admin WHERE email = ?");
$admin->bind_param("s", $email);
$admin->execute();
$admin_result = $admin->get_result();

if ($admin_result->num_rows > 0) {
    $admin_data = $admin_result->fetch_assoc();
    if ($admin_data['password'] === $password) {
        header("Location: ../pages/adminDashboard/admin_dashboard.html");
        exit();
    }
}

// 2. Check Doctor
$doctor = $conn->prepare("SELECT * FROM doctor WHERE email = ?");
$doctor->bind_param("s", $email);
$doctor->execute();
$doctor_result = $doctor->get_result();

if ($doctor_result->num_rows > 0) {
    $doctor_data = $doctor_result->fetch_assoc();
    if ($doctor_data['password'] === $password) {
        header("Location: ../pages/doctorDashboard/doctor_dashboard.html");
        exit();
    }
}

// 3. Check Patient
$patient = $conn->prepare("SELECT * FROM patient WHERE email = ?");
$patient->bind_param("s", $email);
$patient->execute();
$patient_result = $patient->get_result();

if ($patient_result->num_rows > 0) {
    $patient_data = $patient_result->fetch_assoc();

    //  Use password_verify for hashed password
    if (password_verify($password, $patient_data['password'])) {
        header("Location: ../pages/patientDashboard/patient_dashboard.html");
        exit();
    }
}

// If none match
echo "Invalid credentials!";
$conn->close();
?>
