<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

// Optional: check admin session
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    // deny if not admin
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
$action = $_POST['action'] ?? '';

if (!$docid || !in_array($action, ['activate','deactivate'])) {
    http_response_code(400);
    echo 'Invalid input';
    exit;
}

$status = $action === 'activate' ? 'active' : 'inactive';

// Ensure status column exists; use safe check
$check = $conn->query("SHOW COLUMNS FROM doctor LIKE 'status'");
if ($check && $check->num_rows === 0) {
    // Add column
    $conn->query("ALTER TABLE doctor ADD COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'active'");
}

$stmt = $conn->prepare("UPDATE doctor SET status = ? WHERE docid = ?");
if (!$stmt) {
    http_response_code(500);
    echo 'DB error';
    exit;
}
$stmt->bind_param('si', $status, $docid);
$ok = $stmt->execute();
$stmt->close();

if ($ok) {
    header('Location: ../../pages/adminDashboard/admin_viewDoctors.php');
    exit;
} else {
    http_response_code(500);
    echo 'Failed to update status';
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
$action = $_POST['action'] ?? '';

if (!$docid || !in_array($action, ['activate','deactivate'])) {
    http_response_code(400);
    echo 'Invalid input';
    exit;
}

$status = $action === 'activate' ? 'active' : 'inactive';

// ensure status column exists (best-effort)
// MySQL 8 supports IF NOT EXISTS for ADD COLUMN; wrap in try-catch like approach
try {
    $conn->query("ALTER TABLE doctor ADD COLUMN IF NOT EXISTS `status` VARCHAR(20) DEFAULT 'active'");
} catch (Exception $e) {
    // ignore if ALTER fails on older MySQL versions
}

$stmt = $conn->prepare("UPDATE doctor SET status = ? WHERE docid = ?");
if (!$stmt) {
    http_response_code(500);
    echo 'DB error';
    exit;
}
$stmt->bind_param('si', $status, $docid);
$ok = $stmt->execute();
$stmt->close();

if ($ok) {
    header('Location: ../../pages/adminDashboard/admin_dashboard.php');
    exit;
} else {
    http_response_code(500);
    echo 'Failed to update';
}

?>
