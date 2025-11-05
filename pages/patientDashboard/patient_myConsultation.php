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
  <title>Patient My Consultation</title>
  <link rel="stylesheet" href="./patient_CSS/patient_myConsultation.css" />
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
        <a href="./patient_myConsultation.php" class="menu-item active">
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

<!-- Main Content -->
<main class="main-content">
  <h1>My Consultations</h1>

  <div class="consultation-card">
    <div class="consultation-header">
      <div>
        <h2>Doctor Name</h2>
        <p class="specialty">Specialty </p>
      </div>
      <span class="status confirmed">confirmed</span>
    </div>

    <div class="consultation-details">
      <p><img src="../../assets/icons/patientsDashboard/date.svg" alt="date icon"> 2025-01-15</p>
      <p><img src="../../assets/icons/patientsDashboard/time.svg" alt="time icon"> 10:30</p>
      <p><img src="../../assets/icons/patientsDashboard/location.svg" alt="location icon"> Main Clinic - Room 203</p>
    </div>

    <div class="consultation-actions">
      <button class="view-btn">View Details</button>
      <button class="cancel-btn">Cancel</button>
    </div>
  </div>
</main>

  </div>
</body>
</html>
