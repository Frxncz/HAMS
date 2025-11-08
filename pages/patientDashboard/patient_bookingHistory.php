<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'patient') {
  header("Location: ../../login.html");
  exit();
}

$patient_name = $_SESSION['name'];
$patient_id = $_SESSION['user_id'];

include('../../backend/db_connect.php'); // connect to DB

// Fetch all appointments for this patient
$stmt = $conn->prepare("
  SELECT 
    a.id, 
    d.name AS doctor_name, 
    IFNULL(d.specialty, 'General') AS specialty,
    a.appt_date, 
    a.appt_time, 
    a.status
  FROM appointments a
  JOIN doctor d ON a.doctor_id = d.docid
  WHERE a.patient_id = ? AND a.status <> 'declined'
  ORDER BY a.appt_date DESC, a.appt_time DESC
");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

$appointments = [];
while ($row = $result->fetch_assoc()) {
  $appointments[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Patient Booking History</title>
  <link rel="stylesheet" href="./patient_CSS/patient_bookingHistory.css" />
  <link rel="stylesheet" href="../../css/global.css" />
  <link rel="stylesheet" href="../../css/sidebar.css" />
</head>
<body>
  <div class="container">

    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="profile">
        <div class="avatar"><?php echo strtoupper(substr($patient_name, 0, 2)); ?></div>
        <h2><?php echo htmlspecialchars($patient_name); ?></h2>
      </div>

      <button class="logout-btn" onclick="window.location.href='../../backend/logout.php'">
        <img src="../../assets/icons/patientsDashboard/logout.svg" alt="logout icon">
        Log out
      </button>

      <nav class="menu">
        <a href="./patient_dashboard.php" class="menu-item">
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
        <a href="./patient_bookingHistory.php" class="menu-item active">
          <img src="../../assets/icons/patientsDashboard/bookingHistory.svg" alt="history icon">
          Booking History
        </a>
        <a href="./patient_settings.php" class="menu-item">
          <img src="../../assets/icons/patientsDashboard/setting.svg" alt="settings icon">
          Account Settings
        </a>
      </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
      <h2>Booking History</h2>

      <div class="appointments">
        <table>
          <thead>
            <tr>
              <th>Appointment #</th>
              <th>Doctor</th>
              <th>Specialty</th>
              <th>Date</th>
              <th>Time</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($appointments) === 0): ?>
              <tr><td colspan="6">No booking history yet.</td></tr>
            <?php else: ?>
              <?php foreach ($appointments as $a): ?>
                <tr>
                  <td>APT-<?php echo str_pad($a['id'], 3, '0', STR_PAD_LEFT); ?></td>
                  <td><?php echo htmlspecialchars($a['doctor_name']); ?></td>
                  <td><?php echo htmlspecialchars($a['specialty']); ?></td>
                  <td><?php echo htmlspecialchars($a['appt_date']); ?></td>
                  <td><?php echo htmlspecialchars(substr($a['appt_time'], 0, 5)); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</body>
</html>
