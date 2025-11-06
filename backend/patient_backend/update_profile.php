<?php
session_start();

// Ensure user is patient
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'patient') {
    header('Location: ../../login.html');
    exit();
}

include_once __DIR__ . '/../db_connect.php';

$patient_id = $_SESSION['user_id'] ?? null;
$redirect = '../../pages/patientDashboard/patient_settings.php';

if (!$patient_id) {
    header('Location: ' . $redirect . '?error=' . rawurlencode('Not authenticated'));
    exit();
}

$action = $_POST['action'] ?? '';

if ($action === 'info') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($first_name === '' || $last_name === '' || $email === '') {
        header('Location: ' . $redirect . '?error=' . rawurlencode('Please fill all required fields'));
        exit();
    }

    // Check if email is used by another patient
    $chk = $conn->prepare("SELECT pid FROM patient WHERE email = ? AND pid != ?");
    $chk->bind_param("si", $email, $patient_id);
    $chk->execute();
    $chk_res = $chk->get_result();
    if ($chk_res && $chk_res->num_rows > 0) {
        $chk->close();
        header('Location: ' . $redirect . '?error=' . rawurlencode('Email already in use'));
        exit();
    }
    $chk->close();

    $u = $conn->prepare("UPDATE patient SET first_name = ?, last_name = ?, email = ? WHERE pid = ?");
    $u->bind_param("sssi", $first_name, $last_name, $email, $patient_id);
    if ($u->execute()) {
        // Update session name/email
        $_SESSION['name'] = $first_name . ' ' . $last_name;
        $_SESSION['email'] = $email;
        $u->close();
        header('Location: ' . $redirect . '?success=' . rawurlencode('Profile updated'));
        exit();
    } else {
        $u->close();
        header('Location: ' . $redirect . '?error=' . rawurlencode('Update failed'));
        exit();
    }

} elseif ($action === 'password') {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($current === '' || $new === '' || $confirm === '') {
        header('Location: ' . $redirect . '?error=' . rawurlencode('Please fill all password fields'));
        exit();
    }

    if ($new !== $confirm) {
        header('Location: ' . $redirect . '?error=' . rawurlencode('New passwords do not match'));
        exit();
    }

    // Fetch current hash
    $s = $conn->prepare("SELECT password FROM patient WHERE pid = ?");
    $s->bind_param("i", $patient_id);
    $s->execute();
    $res = $s->get_result();
    if (!$res || $res->num_rows === 0) {
        $s->close();
        header('Location: ' . $redirect . '?error=' . rawurlencode('User not found'));
        exit();
    }
    $row = $res->fetch_assoc();
    $s->close();

    $hash = $row['password'];

    if (!password_verify($current, $hash)) {
        header('Location: ' . $redirect . '?error=' . rawurlencode('Current password is incorrect'));
        exit();
    }

    $new_hash = password_hash($new, PASSWORD_DEFAULT);

    $u = $conn->prepare("UPDATE patient SET password = ? WHERE pid = ?");
    $u->bind_param("si", $new_hash, $patient_id);
    if ($u->execute()) {
        $u->close();
        header('Location: ' . $redirect . '?success=' . rawurlencode('Password updated'));
        exit();
    } else {
        $u->close();
        header('Location: ' . $redirect . '?error=' . rawurlencode('Password update failed'));
        exit();
    }

} else {
    header('Location: ' . $redirect);
    exit();
}

?>
