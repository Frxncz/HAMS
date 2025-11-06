<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'doctor') {
    header('Location: ../../login.html');
    exit();
}
$doctor_name = $_SESSION['name'] ?? 'Doctor Name';
$avatar = strtoupper(substr($doctor_name, 0, 2));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Doctor Doctor History</title>
  <link rel="stylesheet" href="./doctor_CSS/doctor_bookingHistory.css" />
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
        <a href="./doctor_dashboard.php" class="menu-item">
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
        <a href="./doctor_bookingHistory.php" class="menu-item active">
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
    <div class="main-content">
      <h1 class="page-title">Booking History</h1>

      <table>
        <thead>
          <tr>
            <th>Appointment #</th>
            <th>Patient Name</th>
            <th>Date</th>
            <th>Time</th>
            <th>Type</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>APT-001</td>
            <td>John Smith</td>
            <td>2024-12-15</td>
            <td>14:00</td>
            <td>In-Person</td>
            <td><span class="status completed">completed</span></td>
            <td><a href="#" class="consult-btn">Consultation History</a></td>
          </tr>
          <tr>
            <td>APT-002</td>
            <td>Sarah Johnson</td>
            <td>2024-11-20</td>
            <td>10:30</td>
            <td>In-Person</td>
            <td><span class="status completed">completed</span></td>
            <td><a href="#" class="consult-btn">Consultation History</a></td>
          </tr>
          <tr>
            <td>APT-003</td>
            <td>Michael Chen</td>
            <td>2024-10-10</td>
            <td>16:00</td>
            <td>In-Person</td>
            <td><span class="status cancelled">cancelled</span></td>
            <td></td>
          </tr>
          <tr>
            <td>APT-004</td>
            <td>Emily Williams</td>
            <td>2024-09-05</td>
            <td>14:30</td>
            <td>In-Person</td>
            <td><span class="status completed">completed</span></td>
            <td><a href="#" class="consult-btn">Consultation History</a></td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>
</body>
</html>
