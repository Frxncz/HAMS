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
  <title>Doctor Settings</title>
  <link rel="stylesheet" href="./doctor_CSS/doctor_settings.css" />
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
        <a href="./doctor_bookingHistory.php" class="menu-item">
          <img src="../../assets/icons/patientsDashboard/bookingHistory.svg" alt="history icon">
          Booking History
        </a>
        <a href="./doctors_settings.php" class="menu-item active">
          <img src="../../assets/icons/patientsDashboard/setting.svg" alt="settings icon">
          Account Settings
        </a>
      </nav>
    </aside>

    <!-- Main Content-->
    <main class="main-content">
      <h1>Account Settings</h1>
      <?php if (isset($_SESSION['msg_success'])): ?>
        <div class="message success"><?php echo htmlspecialchars($_SESSION['msg_success']); unset($_SESSION['msg_success']); ?></div>
      <?php endif; ?>
      <?php if (isset($_SESSION['msg_error'])): ?>
        <div class="message error"><?php echo htmlspecialchars($_SESSION['msg_error']); unset($_SESSION['msg_error']); ?></div>
      <?php endif; ?>
      <?php
      // load current doctor info to prefill the form
      require_once __DIR__ . '/../../backend/db_connect.php';
      $docid = intval($_SESSION['user_id']);
      $doc = [ 'name' => $doctor_name, 'email' => '', 'specialty' => '' ];
      if ($stmt = $conn->prepare('SELECT name, email, specialty FROM doctor WHERE docid = ? LIMIT 1')) {
        $stmt->bind_param('i', $docid);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $row = $res->fetch_assoc()) {
          $doc = array_merge($doc, $row);
        }
        $stmt->close();
      }
      // split name to first/last for fields
    $parts = explode(' ', $doc['name'], 2);
      $first = $parts[0] ?? '';
      $last = $parts[1] ?? '';
      ?>

      <!-- Personal Information -->
      <section class="settings-section">
        <h2>Personal Information</h2>
        <p class="section-subtitle">Update your personal details</p>
        <form method="post" action="../../backend/doctor_backend/update_doctor_profile.php">
          <input type="hidden" name="action" value="info">
          <div class="form-row">
            <div class="form-group">
              <label for="first-name">First Name</label>
              <input type="text" id="first-name" name="first_name" value="<?php echo htmlspecialchars($first); ?>">
            </div>
            <div class="form-group">
              <label for="last-name">Last Name</label>
              <input type="text" id="last-name" name="last_name" value="<?php echo htmlspecialchars($last); ?>">
            </div>
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($doc['email']); ?>">
          </div>

          <div class="form-group">
            <label for="specialty">Specialty</label>
            <input type="text" id="specialty" name="specialty" value="<?php echo htmlspecialchars($doc['specialty']); ?>">
          </div>

          <button type="submit" class="save-btn">Save Changes</button>
        </form>
      </section>
      <section class="settings-section">
        <h2>Security</h2>
        <p class="section-subtitle">Update your password</p>
        <form method="post" action="../../backend/doctor_backend/update_doctor_profile.php">
          <input type="hidden" name="action" value="password">
          <div class="form-group">
            <label for="current-password">Current Password</label>
            <input type="password" id="current-password" name="current_password" required>
          </div>

          <div class="form-group">
            <label for="new-password">New Password</label>
            <input type="password" id="new-password" name="new_password" required>
          </div>

          <div class="form-group">
            <label for="confirm-password">Confirm New Password</label>
            <input type="password" id="confirm-password" name="confirm_password" required>
          </div>

          <button type="submit" class="save-btn">Update Password</button>
        </form>
      </section>
    </main>

  </div>
</body>
</html>
