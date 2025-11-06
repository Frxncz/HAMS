<?php
  session_start();

  // Redirect to login if not logged in
  if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: ../../login.html");
    exit();
  }

  // Load DB connection and current patient data
  include_once __DIR__ . '/../../backend/db_connect.php';

  $patient_name = $_SESSION['name'];
  $patient_email = $_SESSION['email'] ?? '';
  $patient_id = $_SESSION['user_id'] ?? null;

  $first_name = '';
  $last_name = '';

  if ($patient_id) {
    $stmt = $conn->prepare("SELECT * FROM patient WHERE pid = ?");
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
      $row = $res->fetch_assoc();
      $first_name = htmlspecialchars($row['first_name']);
      $last_name = htmlspecialchars($row['last_name']);
      $patient_email = htmlspecialchars($row['email']);
    }
    $stmt->close();
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Patient Settings</title>
  <link rel="stylesheet" href="./patient_CSS/patient_settings.css" />
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
        <a href="./patient_bookingHistory.php" class="menu-item">
          <img src="../../assets/icons/patientsDashboard/bookingHistory.svg" alt="history icon">
          Booking History
        </a>
        <a href="./patient_settings.php" class="menu-item active">
          <img src="../../assets/icons/patientsDashboard/setting.svg" alt="settings icon">
          Account Settings
        </a>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <h1>Account Settings</h1>
      <?php if (!empty($_GET['success'])): ?>
        <div class="message success"><?php echo htmlspecialchars($_GET['success']); ?></div>
      <?php endif; ?>
      <?php if (!empty($_GET['error'])): ?>
        <div class="message error"><?php echo htmlspecialchars($_GET['error']); ?></div>
      <?php endif; ?>

      <!-- Personal Information -->
      <section class="settings-section">
        <h2>Personal Information</h2>
        <p class="section-subtitle">Update your personal details</p>
        <form action="../../backend/patient_backend/update_profile.php" method="POST">
          <div class="form-row">
            <div class="form-group">
              <label for="first-name">First Name</label>
              <input type="text" id="first-name" name="first_name" value="<?php echo $first_name; ?>" required>
            </div>
            <div class="form-group">
              <label for="last-name">Last Name</label>
              <input type="text" id="last-name" name="last_name" value="<?php echo $last_name; ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo $patient_email; ?>" required>
          </div>

          <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" placeholder="+1 (555) 123-4567">
          </div>

          <input type="hidden" name="action" value="info">
          <button type="submit" class="save-btn">Save Changes</button>
        </form>
      </section>

      <!-- Address -->
      <!-- Address section removed (not present in DB schema). Add later if columns are added. -->

      <!-- Security -->
      <section class="settings-section">
        <h2>Security</h2>
        <p class="section-subtitle">Update your password</p>
        <form action="../../backend/patient_backend/update_profile.php" method="POST">
          <div class="form-group">
            <label for="current-password">Current Password</label>
            <input type="password" id="current-password" name="current_password">
          </div>

          <div class="form-group">
            <label for="new-password">New Password</label>
            <input type="password" id="new-password" name="new_password">
          </div>

          <div class="form-group">
            <label for="confirm-password">Confirm New Password</label>
            <input type="password" id="confirm-password" name="confirm_password">
          </div>
          <input type="hidden" name="action" value="password">
          <button type="submit" class="save-btn">Update Password</button>
        </form>
      </section>
    </main>

  </div>
</body>
</html>
