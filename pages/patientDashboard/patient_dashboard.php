<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'patient') {
  header("Location: ../../login.html");
  exit();
}

$patient_name = $_SESSION['name'];
$patient_id = $_SESSION['user_id'];

include('../../backend/db_connect.php');

// Load doctors for dropdown
$doctors = [];
$docRes = $conn->query("SELECT docid, name FROM doctor ORDER BY name ASC");

if (!$docRes) {
    die("Doctor query failed: " . $conn->error);
}

if ($docRes->num_rows > 0) {
    while ($r = $docRes->fetch_assoc()) {
        $doctors[] = $r;
    }
} else {
    $doctors = [
        ["docid" => 1, "name" => "Dr. John Doe"],
        ["docid" => 2, "name" => "Dr. Sarah Cruz"]
    ];
}

// Load this patient's appointments
$appointments = [];
$stmt = $conn->prepare("SELECT a.id, a.appt_date, a.appt_time, a.purpose, a.status, d.name AS doctor_name
                        FROM appointments a
                        JOIN doctor d ON a.doctor_id = d.docid
                        WHERE a.patient_id = ?
                        ORDER BY a.appt_date DESC, a.appt_time DESC");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $appointments[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Patient Dashboard</title>
  <link rel="stylesheet" href="./patient_CSS/patient_dashboard.css" />
  <link rel="stylesheet" href="../../css/global.css" />
  <link rel="stylesheet" href="../../css/sidebar.css" />
  <link rel="stylesheet" href="./patient_CSS/modal.css" />
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="profile">
        <div class="avatar"><?php echo strtoupper(substr($patient_name,0,2)); ?></div>
        <h2><?php echo htmlspecialchars($patient_name); ?></h2>
      </div>
      <button class="logout-btn" onclick="window.location.href='../../backend/logout.php'">
        <img src="../../assets/icons/patientsDashboard/logout.svg" alt="logout icon"> Log out
      </button>

      <nav class="menu">
        <a href="./patient_dashboard.php" class="menu-item active">
          <img src="../../assets/icons/patientsDashboard/Home.svg" alt="home icon">
          Home
        </a>
        <a href="./patient_find-doctor.php" class="menu-item">
          <img src="../../assets/icons/patientsDashboard/findDoctor.svg" alt="doctor icon">
          Find Doctor
        </a>
        <a href="./patient_myConsultation.php" class="menu-item">
          <img src="../../assets/icons/patientsDashboard/myConsultation.svg" alt="consultations icon">
          My Consultations
        </a>
        <a href="./patient_bookingHistory.php" class="menu-item">
          <img src="../../assets/icons/patientsDashboard/bookingHistory.svg" alt="history icon">
          Booking History
        </a>
        <a href="./patient_settings.php" class="menu-item">
          <img src="../../assets/icons/patientsDashboard/setting.svg" alt="settings icon">
          Account Settings
        </a>
      </nav>
    </aside>

    <main class="main-content">
      <section class="welcome">
        <h2>Welcome!</h2>
        <p class="patient-name"><?php echo htmlspecialchars($patient_name); ?></p>
        <p>We're glad you're here! ...</p>
        <button class="schedule-btn" id="openScheduleBtn">Schedule Now</button>
      </section>

      <section class="appointments">
        <h3>Your Upcoming Appointments</h3>

        <?php if (isset($_SESSION['appt_success'])): ?>
          <div class="message success"><?php echo $_SESSION['appt_success']; unset($_SESSION['appt_success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['appt_error'])): ?>
          <div class="message error"><?php echo $_SESSION['appt_error']; unset($_SESSION['appt_error']); ?></div>
        <?php endif; ?>

        <table>
          <thead>
            <tr>
              <th>Appoint. #</th>
              <th>Doctor</th>
              <th>Date</th>
              <th>Time</th>
              <th>Purpose</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($appointments) === 0): ?>
              <tr><td colspan="6">No appointments yet.</td></tr>
            <?php else: ?>
              <?php foreach($appointments as $a): ?>
                <tr>
                  <td><?php echo htmlspecialchars($a['id']); ?></td>
                  <td><?php echo htmlspecialchars($a['doctor_name']); ?></td>
                  <td><?php echo htmlspecialchars($a['appt_date']); ?></td>
                  <td><?php echo htmlspecialchars(substr($a['appt_time'],0,5)); ?></td>
                  <td><?php echo htmlspecialchars($a['purpose']); ?></td>
                  <td><?php echo htmlspecialchars($a['status']); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>

  <!-- Include Modal HTML -->
  <?php include('./appointment_modal.html'); ?>

  <!-- Include Modal JavaScript -->
  <script src="./patient_JS/modal.js"></script>
</body>
</html>