<?php
session_start();

// Ensure user is doctor; otherwise redirect to login
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'doctor') {
    header('Location: ../../login.html');
    exit();
}

$doctor_name = $_SESSION['name'] ?? 'Doctor Name';
$avatar = strtoupper(substr($doctor_name, 0, 2));
// DB connection and fetch upcoming appointments for this doctor
include_once __DIR__ . '/../../backend/db_connect.php';
$doctor_id = $_SESSION['user_id'] ?? $_SESSION['doctor_id'] ?? $_SESSION['docid'] ?? null;
$appointments = [];
// Fetch fresh doctor profile so admin edits reflect immediately
if ($doctor_id) {
  if ($pstmt = $conn->prepare("SELECT name, email, specialty, COALESCE(status,'active') AS status FROM doctor WHERE docid = ?")) {
    $pstmt->bind_param('i', $doctor_id);
    $pstmt->execute();
    $pres = $pstmt->get_result();
    if ($prow = $pres->fetch_assoc()) {
      $doctor_name = $prow['name'] ?: $doctor_name;
      $avatar = strtoupper(substr($doctor_name, 0, 2));
      $_SESSION['name'] = $doctor_name;
      $_SESSION['email'] = $prow['email'] ?? $_SESSION['email'] ?? '';
    }
    $pstmt->close();
  }
}
if ($doctor_id) {
  $stmt = $conn->prepare(
    "SELECT a.appt_date, a.appt_time, a.purpose, a.status, p.first_name, p.last_name
     FROM appointments a
     JOIN patient p ON a.patient_id = p.pid
    -- exclude declined appointments so they don't appear in upcoming list
    WHERE a.doctor_id = ? AND a.status <> 'declined' AND a.appt_date >= CURDATE()
     ORDER BY a.appt_date ASC, a.appt_time ASC"
  );
  if ($stmt) {
    $stmt->bind_param('i', $doctor_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
      $appointments[] = $r;
    }
    $stmt->close();
  }

  // Total appointments for this doctor counted as one per patient (distinct patient_id)
  $totalAppointmentsDistinct = 0;
  if ($stmt2 = $conn->prepare("SELECT COUNT(DISTINCT patient_id) AS cnt FROM appointments WHERE doctor_id = ? AND status <> 'declined'")) {
    $stmt2->bind_param('i', $doctor_id);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    $r2 = $res2->fetch_assoc();
    $totalAppointmentsDistinct = intval($r2['cnt'] ?? 0);
    $stmt2->close();
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Doctor Dashboard</title>
  <link rel="stylesheet" href="./doctor_CSS/doctor_dashboard.css" />
  <link rel="stylesheet" href="../../css/global.css" />
  <link rel="stylesheet" href="../../css/sidebar.css" />
</head>
<body>
  <div class="container">

    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="profile">
        <div class="avatar"><?php echo htmlspecialchars($avatar); ?></div>
        <h2><?php echo htmlspecialchars($doctor_name); ?></h2>
      </div>
      <button class="logout-btn" onclick="window.location.href='../../backend/logout.php'">
        <img src="../../assets/icons/patientsDashboard/logout.svg" alt="logout icon">
        Log out
      </button>

      <nav class="menu">
        <a href="./doctor_dashboard.php" class="menu-item active">
          <img src="../../assets/icons/patientsDashboard/Home.svg" alt="home icon">
          Dashboard
        </a>
        <a href="./doctor_myAppointments.php" class="menu-item">
          <img src="../../assets/icons/patientsDashboard/findDoctor.svg" alt="appointments icon">
          My Appointments
        </a>
        <a href="./doctor_mySessions.php" class="menu-item">
          <img src="../../assets/icons/patientsDashboard/myConsultation.svg" alt="sessions icon">
          My Sessions
        </a>
        <a href="./doctor_bookingHistory.php" class="menu-item">
          <img src="../../assets/icons/patientsDashboard/bookingHistory.svg" alt="history icon">
          Booking History
        </a>
        <a href="./doctors_settings.php" class="menu-item">
          <img src="../../assets/icons/patientsDashboard/setting.svg" alt="settings icon">
          Account Settings
        </a>
      </nav>
    </aside>

    <!-- Main Content-->
    <main class="main-content">
      <section class="welcome">
        <h2>Welcome!</h2>
        <p class="doctor-name"><?php echo htmlspecialchars($doctor_name); ?></p>
        <p>
          Manage your appointments and provide excellent care to your patients.
          Stay organized and keep track of your schedule.
        </p>
        <button class="appointments-btn">View My Appointments</button>
      </section>

      <!-- Quick Stats -->
      <section class="stats">
        <div class="stats-grid" style="display:flex; gap:16px; margin:18px 0;">
          <div class="stat-card" style="padding:12px; border-radius:8px; background:#fff; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
            <div style="display:flex; align-items:center; gap:10px;">
              <div style="width:44px; height:44px; border-radius:50%; background:#f0f4ff; display:flex; align-items:center; justify-content:center;">
                <img src="../../assets/icons/patientsDashboard/findDoctor.svg" alt="appointments icon" style="width:22px; height:22px;">
              </div>
              <div>
                <div style="font-size:12px; color:#666;">Total Appointments</div>
                <div style="font-size:20px; font-weight:600;"><?php echo htmlspecialchars($totalAppointmentsDistinct); ?></div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="appointments">
        <h3>Your Upcoming Appointments</h3>
        <table>
          <thead>
            <tr>
              <th>Patient Name</th>
              <th>Scheduled Date</th>
              <th>Time</th>
              <th>Purpose of Visit</th>
            </tr>
          </thead>
          <tbody>
          <?php if (!empty($appointments)): ?>
            <?php foreach ($appointments as $appt): ?>
              <tr>
                <td><?php echo htmlspecialchars($appt['first_name'] . ' ' . $appt['last_name']); ?></td>
                <td><?php echo htmlspecialchars($appt['appt_date']); ?></td>
                <td><?php echo htmlspecialchars(substr($appt['appt_time'], 0, 5)); ?></td>
                <td><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $appt['purpose']))); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="4">No upcoming appointments.</td>
            </tr>
          <?php endif; ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>
</body>
</html>
