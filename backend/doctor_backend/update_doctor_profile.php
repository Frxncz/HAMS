<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'doctor') {
    header('Location: ../../login.html');
    exit();
}

require_once __DIR__ . '/../db_connect.php';

$docid = intval($_SESSION['user_id']);
$action = $_POST['action'] ?? '';

if ($action === 'info') {
    $first = trim($_POST['first_name'] ?? '');
    $last = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $specialty = trim($_POST['specialty'] ?? '');

    if ($first === '' || $last === '' || $email === '') {
        $_SESSION['msg_error'] = 'Please fill first name, last name and email.';
        header('Location: ../../pages/doctorDashboard/doctors_settings.php');
        exit();
    }

    // check email uniqueness among doctors (excluding current)
    $chk = $conn->prepare('SELECT docid FROM doctor WHERE email = ? AND docid <> ? LIMIT 1');
    if ($chk) {
        $chk->bind_param('si', $email, $docid);
        $chk->execute();
        $r = $chk->get_result();
        if ($r && $r->num_rows > 0) {
            $_SESSION['msg_error'] = 'Email already in use by another doctor.';
            $chk->close();
            header('Location: ../../pages/doctorDashboard/doctors_settings.php');
            exit();
        }
        $chk->close();
    }

    $name = $first . ' ' . $last;
    $upd = $conn->prepare('UPDATE doctor SET name = ?, email = ?, specialty = ? WHERE docid = ?');
    if (!$upd) {
        error_log('Prepare failed: ' . $conn->error);
        $_SESSION['msg_error'] = 'Server error.';
        header('Location: ../../pages/doctorDashboard/doctors_settings.php');
        exit();
    }
    $upd->bind_param('sssi', $name, $email, $specialty, $docid);
    if ($upd->execute()) {
        $_SESSION['msg_success'] = 'Profile updated successfully.';
        // update session name/email
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
    } else {
        error_log('Execute failed: ' . $upd->error);
        $_SESSION['msg_error'] = 'Failed to update profile.';
    }
    $upd->close();

    header('Location: ../../pages/doctorDashboard/doctors_settings.php');
    exit();
}

if ($action === 'password') {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($new === '' || $confirm === '' || $current === '') {
        $_SESSION['msg_error'] = 'Please fill all password fields.';
        header('Location: ../../pages/doctorDashboard/doctors_settings.php');
        exit();
    }
    if ($new !== $confirm) {
        $_SESSION['msg_error'] = 'New password and confirmation do not match.';
        header('Location: ../../pages/doctorDashboard/doctors_settings.php');
        exit();
    }

    // fetch stored password
    $s = $conn->prepare('SELECT password FROM doctor WHERE docid = ? LIMIT 1');
    if (!$s) {
        error_log('Prepare failed: ' . $conn->error);
        $_SESSION['msg_error'] = 'Server error.';
        header('Location: ../../pages/doctorDashboard/doctors_settings.php');
        exit();
    }
    $s->bind_param('i', $docid);
    $s->execute();
    $res = $s->get_result();
    if (!$res || $res->num_rows === 0) {
        $s->close();
        $_SESSION['msg_error'] = 'Doctor account not found.';
        header('Location: ../../pages/doctorDashboard/doctors_settings.php');
        exit();
    }
    $row = $res->fetch_assoc();
    $stored = $row['password'];
    $s->close();

    $is_hashed = false;
    if (strlen($stored) > 0 && (strpos($stored, '$2y$') === 0 || strpos($stored, '$argon2') === 0 || password_get_info($stored)['algo'])) {
        // try password_verify; if it succeeds, treat as hashed
        if (password_verify($current, $stored)) {
            $is_hashed = true;
        }
    }

    $current_ok = false;
    if ($is_hashed) {
        $current_ok = password_verify($current, $stored);
    } else {
        // stored in plaintext (legacy)
        $current_ok = ($current === $stored);
    }

    if (!$current_ok) {
        $_SESSION['msg_error'] = 'Current password is incorrect.';
        header('Location: ../../pages/doctorDashboard/doctors_settings.php');
        exit();
    }

    // decide how to store new password: if existing was hashed, hash; otherwise keep plaintext for compatibility
    if ($is_hashed) {
        $to_store = password_hash($new, PASSWORD_DEFAULT);
    } else {
        $to_store = $new; // legacy plaintext storage to match existing login flow
    }

    $u = $conn->prepare('UPDATE doctor SET password = ? WHERE docid = ?');
    if (!$u) {
        error_log('Prepare failed: ' . $conn->error);
        $_SESSION['msg_error'] = 'Server error.';
        header('Location: ../../pages/doctorDashboard/doctors_settings.php');
        exit();
    }
    $u->bind_param('si', $to_store, $docid);
    if ($u->execute()) {
        $_SESSION['msg_success'] = 'Password updated successfully.';
    } else {
        error_log('Execute failed: ' . $u->error);
        $_SESSION['msg_error'] = 'Failed to update password.';
    }
    $u->close();

    header('Location: ../../pages/doctorDashboard/doctors_settings.php');
    exit();
}

// unknown action
$_SESSION['msg_error'] = 'Invalid action.';
header('Location: ../../pages/doctorDashboard/doctors_settings.php');
exit();
