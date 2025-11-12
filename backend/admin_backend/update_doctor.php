<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method not allowed';
    exit;
}

$docid = isset($_POST['docid']) ? intval($_POST['docid']) : 0;
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$specialty = trim($_POST['specialty'] ?? '');
$password = $_POST['password'] ?? '';

if (!$docid || $name === '' || $email === '') {
    http_response_code(400);
    echo 'Invalid input';
    exit;
}

// If updating email, ensure uniqueness (other than current)
$check = $conn->prepare("SELECT docid FROM doctor WHERE email = ? AND docid <> ?");
$check->bind_param('si', $email, $docid);
$check->execute();
$cres = $check->get_result();
if ($cres->num_rows > 0) {
    http_response_code(409);
    echo 'Email already in use';
    exit;
}
$check->close();

if ($password !== '') {
    $stmt = $conn->prepare("UPDATE doctor SET name = ?, email = ?, specialty = ?, password = ? WHERE docid = ?");
    $stmt->bind_param('ssssi', $name, $email, $specialty, $password, $docid);
} else {
    $stmt = $conn->prepare("UPDATE doctor SET name = ?, email = ?, specialty = ? WHERE docid = ?");
    $stmt->bind_param('sssi', $name, $email, $specialty, $docid);
}

if (!$stmt) {
    http_response_code(500);
    echo 'DB error';
    exit;
}

$ok = $stmt->execute();
$stmt->close();

if ($ok) {
    header('Location: ../../pages/adminDashboard/admin_viewDoctors.php');
    exit;
} else {
    http_response_code(500);
    echo 'Failed to update doctor';
}

?>
<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method not allowed';
    exit;
}

$docid = isset($_POST['docid']) ? intval($_POST['docid']) : 0;
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$specialty = trim($_POST['specialty'] ?? '');
$password = $_POST['password'] ?? '';

if (!$docid || $name === '' || $email === '') {
    http_response_code(400);
    echo 'Invalid input';
    exit;
}

// update statement
if ($password !== '') {
    // update including password (store plaintext to match current project or hash?)
    $stmt = $conn->prepare("UPDATE doctor SET name = ?, email = ?, specialty = ?, password = ? WHERE docid = ?");
    $stmt->bind_param('ssssi', $name, $email, $specialty, $password, $docid);
} else {
    $stmt = $conn->prepare("UPDATE doctor SET name = ?, email = ?, specialty = ? WHERE docid = ?");
    $stmt->bind_param('sssi', $name, $email, $specialty, $docid);
}

if (!$stmt) {
    http_response_code(500);
    echo 'DB error';
    exit;
}

$ok = $stmt->execute();
$stmt->close();

if ($ok) {
    // If the doctor currently has an active session, we cannot directly update their session from here easily.
    // Doctor pages should read fresh data from DB; advise doctor to re-login to refresh session-based values.
    header('Location: ../../pages/adminDashboard/admin_dashboard.php');
    exit;
} else {
    http_response_code(500);
    echo 'Failed to update doctor';
}

?>
