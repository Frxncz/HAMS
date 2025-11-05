<?php
  session_start();

  // Redirect to login if not logged in
  if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: ../../login.html");
    exit();
  }

  $patient_name = $_SESSION['name'];
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
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>APT-001</td>
              <td>Dr. Sarah Johnson</td>
              <td>Cardiologist</td>
              <td>2024-12-15</td>
              <td>14:00</td>
              <td><span class="status completed">completed</span></td>
              <td><button class="action-btn">Book Again</button></td>
            </tr>
            <tr>
              <td>APT-002</td>
              <td>Dr. Michael Chen</td>
              <td>General Practitioner</td>
              <td>2024-11-20</td>
              <td>10:30</td>
              <td><span class="status completed">completed</span></td>
              <td><button class="action-btn">Book Again</button></td>
            </tr>
            <tr>
              <td>APT-003</td>
              <td>Dr. Emily Williams</td>
              <td>Pediatrician</td>
              <td>2024-10-10</td>
              <td>16:00</td>
              <td><span class="status cancelled">cancelled</span></td>
              <td><button class="action-btn">Book Again</button></td>
            </tr>
            <tr>
              <td>APT-004</td>
              <td>Dr. Sarah Johnson</td>
              <td>Cardiologist</td>
              <td>2024-09-05</td>
              <td>14:30</td>
              <td><span class="status completed">completed</span></td>
              <td><button class="action-btn">Book Again</button></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

 
  </div>
</body>
</html>
