<?php
include('db_connect.php');

// Get form data safely
$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Validate input
if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
    die("All fields are required!");
}

// Check if email already exists
$check = $conn->prepare("SELECT * FROM patient WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    die("Email already registered!");
}

// Hash the password before saving (important)
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert new record
$stmt = $conn->prepare("INSERT INTO patient (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $first_name, $last_name, $email, $hashed_password);

if ($stmt->execute()) {
    header("Location: ../login.html");
    exit();
} else {
    die("Error: " . $stmt->error);
}

$conn->close();
?>
