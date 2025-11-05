<?php
session_start();
include('db_connect.php');

$email = $_POST['email'];
$password = $_POST['password'];

if (empty($email) || empty($password)) {
    die("Please fill all fields.");
}

/* ---- ADMIN ---- */
$admin = $conn->prepare("SELECT * FROM admin WHERE email = ?");
$admin->bind_param("s", $email);
$admin->execute();
$admin_result = $admin->get_result();

if ($admin_result->num_rows > 0) {
    $admin_data = $admin_result->fetch_assoc();

    if ($admin_data['password'] === $password) {
        $_SESSION['user_id'] = $admin_data['id'];
        $_SESSION['user_type'] = 'admin';
        $_SESSION['name'] = $admin_data['name'];
        header("Location: ../pages/adminDashboard/admin_dashboard.html");
        exit();
    }
}

/* ---- DOCTOR ---- */
$doctor = $conn->prepare("SELECT * FROM doctor WHERE email = ?");
$doctor->bind_param("s", $email);
$doctor->execute();
$doctor_result = $doctor->get_result();

if ($doctor_result->num_rows > 0) {
    $doctor_data = $doctor_result->fetch_assoc();

    if ($doctor_data['password'] === $password) {
        $_SESSION['user_id'] = $doctor_data['id'];
        $_SESSION['user_type'] = 'doctor';
        $_SESSION['name'] = $doctor_data['name'];
        header("Location: ../pages/doctorDashboard/doctor_dashboard.html");
        exit();
    }
}

/* ---- PATIENT ---- */
$patient = $conn->prepare("SELECT * FROM patient WHERE email = ?");
$patient->bind_param("s", $email);
$patient->execute();
$patient_result = $patient->get_result();

if ($patient_result->num_rows > 0) {
    $patient_data = $patient_result->fetch_assoc();

    if (password_verify($password, $patient_data['password'])) {
        $_SESSION['user_id'] = $patient_data['pid']; // or 'pid' if that's your column
        $_SESSION['user_type'] = 'patient';
        $_SESSION['name'] = $patient_data['first_name'] . ' ' . $patient_data['last_name'];
        $_SESSION['email'] = $patient_data['email'];

        header("Location: ../pages/patientDashboard/patient_dashboard.php");
        exit();
    }
}

/* ---- If login fails ---- */
echo "Invalid credentials!";
$conn->close();
?>
