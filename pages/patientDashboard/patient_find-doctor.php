<?php
  session_start();

  include('../../backend/db_connect.php');

  // Redirect to login if not logged in
  if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: ../../login.html");
    exit();
  }

  $patient_name = $_SESSION['name'];

    $doctors = [];
  $result = $conn->query("SELECT * FROM doctor ORDER BY name ASC");

  if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $doctors[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Patient Find Doctor</title>
  <link rel="stylesheet" href="./patient_CSS/patient_find-doctor.css" />
  <link rel="stylesheet" href="../../css/global.css" />
  <link rel="stylesheet" href="../../css/sidebar.css" />
</head>

<script>
function bookDoctor(docid) {
  window.location.href = `./patient_dashboard.php?doctor_id=${docid}`;
}
</script>

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
        <a href="./patient_find-doctor.php" class="menu-item active">
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

    <!-- Main Content -->
    <main class="main-content">
        <h1>Find Doctor</h1>

        <div class="search-bar">
            <input type="text" placeholder="Search by name or specialty..." />
        </div>

<div class="doctor-list">
  <?php if (count($doctors) === 0): ?>
    <p>No doctors available at the moment.</p>
  <?php else: ?>
    <?php foreach ($doctors as $doc): ?>
      <div class="doctor-card">
        <div class="doctor-info">
          <h2><?php echo htmlspecialchars($doc['name']); ?></h2>
          <p class="specialty"><?php echo htmlspecialchars($doc['specialty']); ?></p>
          <p class="hospital">
            <img src="../../assets/icons/patientsDashboard/location.svg" alt="location icon">
            Medical Center A
          </p>
          <p class="availability">
            <img src="../../assets/icons/patientsDashboard/time.svg" alt="time icon">
            Available Today
          </p>
        </div>
        <button class="book-btn" onclick="bookDoctor(<?php echo $doc['docid']; ?>)">Book Appointment</button>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

    </main>

  </div>
</body>
</html>
