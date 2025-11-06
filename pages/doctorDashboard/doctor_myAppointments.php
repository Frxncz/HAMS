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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Doctor My Appointments</title>
  <link rel="stylesheet" href="./doctor_CSS/doctor_myAppointments.css" />
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
        <a href="./doctor_myAppointments.php" class="menu-item active">
          <img src="../../assets/icons/patientsDashboard/findDoctor.svg" alt="appointment icon">
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

    <!-- Main Content -->
    <main class="main-content">
      <h1>My Appointments</h1>

      <div class="appointments-list">
        <!-- Appointment Card -->
        <div class="appointment-card">
          <div class="card-info">
            <div>
              <h2>John Smith</h2>
              <p class="purpose">Regular Checkup</p>
            </div>
            <span class="status confirmed">confirmed</span>
          </div>
          <div class="card-details">
            <p><img src="../../assets/icons/doctorDashboard/date.svg" alt="date"> 2025-10-22</p>
            <p><img src="../../assets/icons/doctorDashboard/time.svg" alt="time">09:00</p>
            <p><img src="../../assets/icons/doctorDashboard/person.svg" alt="person">In-Person</p>
          </div>
          <div class="card-actions">
            <button class="view-btn">View Details</button>
          </div>
        </div>

        <div class="appointment-card">
          <div class="card-info">
            <div>
              <h2>Sarah Johnson</h2>
              <p class="purpose">Follow-up Consultation</p>
            </div>
            <span class="status confirmed">confirmed</span>
          </div>
          <div class="card-details">
            <p><img src="../../assets/icons/doctorDashboard/date.svg" alt="date">2025-10-22</p>
            <p><img src="../../assets/icons/doctorDashboard/time.svg" alt="time">10:30</p>
            <p><img src="../../assets/icons/doctorDashboard/person.svg" alt="person">In-Person</p>
          </div>
          <div class="card-actions">
            <button class="view-btn">View Details</button>
          </div>
        </div>

        <div class="appointment-card">
          <div class="card-info">
            <div>
              <h2>Michael Chen</h2>
              <p class="purpose">Initial Consultation</p>
            </div>
            <span class="status pending">pending</span>
          </div>
          <div class="card-details">
            <p><img src="../../assets/icons/doctorDashboard/date.svg" alt="date">2025-10-23</p>
            <p><img src="../../assets/icons/doctorDashboard/time.svg" alt="time">14:00</p>
            <p><img src="../../assets/icons/doctorDashboard/person.svg" alt="person">In-Person</p>
          </div>
          <div class="card-actions">
            <button class="view-btn">View Details</button>
            <button class="accept-btn">Accept</button>
            <button class="decline-btn">Decline</button>
          </div>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
