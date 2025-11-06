<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'patient') {
  header("Location: ../../login.html");
  exit();
}

include('../../backend/db_connect.php');
$patient_id = $_SESSION['user_id'];
$patient_name = $_SESSION['name'];

// Fetch appointments
$stmt = $conn->prepare("SELECT a.id, a.appt_date, a.appt_time, a.purpose, a.status,
                               d.name AS doctor_name, d.specialty
                        FROM appointments a
                        JOIN doctor d ON a.doctor_id = d.docid
                        WHERE a.patient_id = ?
                        ORDER BY a.appt_date DESC");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$appointments = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Appointments</title>
  <link rel="stylesheet" href="./patient_CSS/patient_myConsultation.css" />
  <link rel="stylesheet" href="../../css/global.css" />
  <link rel="stylesheet" href="../../css/sidebar.css" />
  <script src="./patient_JS/patient_myConsultation.js"></script>

</head>
<body>

    <!-- Appointment Details Modal -->
  <div id="detailsModal" class="modal-backdrop">
    <div class="modal">
      <h3>Appointment Details</h3>
      <div id="modalContent"></div>
      <div class="actions">
        <button class="btn ghost" onclick="closeModal()">Close</button>
      </div>
    </div>
  </div>

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
      <h1>My Appointments</h1>

      <?php if (count($appointments) === 0): ?>
        <p>No appointments found.</p>
      <?php else: ?>
        <?php foreach ($appointments as $appt): ?>
          <div class="consultation-card">
            <div class="consultation-header">
              <div>
                <h2><?php echo htmlspecialchars($appt['doctor_name']); ?></h2>
                <p class="specialty"><?php echo htmlspecialchars($appt['specialty'] ?? 'General Practitioner'); ?></p>
              </div>
              <span class="status <?php echo strtolower($appt['status']); ?>">
                <?php echo htmlspecialchars($appt['status']); ?>
              </span>
            </div>

            <div class="consultation-details">
              <p><strong>Date:</strong> <?php echo htmlspecialchars($appt['appt_date']); ?></p>
              <p><strong>Time:</strong> <?php echo htmlspecialchars(substr($appt['appt_time'], 0, 5)); ?></p>
              <p><strong>Purpose:</strong> <?php echo htmlspecialchars($appt['purpose']); ?></p>
            </div>

            <div class="consultation-actions">
            <button class="view-btn" onclick='openDetailsModal(<?php echo json_encode($appt); ?>)'> View Details </button>
              <button class="cancel-btn" onclick="cancelAppointment(<?php echo $appt['id']; ?>)">Cancel</button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </main>
  </div>

  <!-- Modal Form -->
  <div id="detailsForm" class="modal-backdrop">
    <div class="modal">
      <h3>Appointment Information</h3>
      <form id="apptDetailsForm">
        <div class="form-group">
          <label>Doctor's Name</label>
          <input type="text" id="doctor_name" readonly>
        </div>
        <div class="form-group">
          <label>Specialty</label>
          <input type="text" id="specialty" readonly>
        </div>
        <div class="form-group">
          <label>Date</label>
          <input type="text" id="appt_date" readonly>
        </div>
        <div class="form-group">
          <label>Time</label>
          <input type="text" id="appt_time" readonly>
        </div>
        <div class="form-group">
          <label>Purpose</label>
          <input type="text" id="purpose" readonly>
        </div>
        <div class="form-group">
          <label>Status</label>
          <input type="text" id="status" readonly>
        </div>
        <div class="actions">
          <button type="button" class="btn ghost" onclick="closeForm()">Close</button>
        </div>
      </form>
    </div>
  </div>

  <script src="./patient_JS/patient_myConsultation.js"></script>
</body>
</html>
