<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'doctor') {
    header('Location: ../../login.html');
    exit();
}
$doctor_name = $_SESSION['name'] ?? 'Doctor Name';
$avatar = strtoupper(substr($doctor_name, 0, 2));
// include DB connection
require_once __DIR__ . '/../../backend/db_connect.php';

// fetch appointments for logged-in doctor
$doctor_id = intval($_SESSION['user_id']);
$appointments = [];
// exclude appointments that have been declined so they don't show in the doctor's list
if ($stmt = $conn->prepare("SELECT a.id, a.appt_date, a.appt_time, a.purpose, a.status, p.pid AS patient_id, p.first_name, p.last_name, p.email FROM appointments a JOIN patient p ON a.patient_id = p.pid WHERE a.doctor_id = ? AND a.status <> 'declined' ORDER BY a.appt_date ASC, a.appt_time ASC")) {
  $stmt->bind_param('i', $doctor_id);
  $stmt->execute();
  $result = $stmt->get_result();
  while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
  }
  $stmt->close();
}
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
  <style>
    /* Simple modal styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 2000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.5);
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .modal.open { display: flex; }
    .modal-box {
      background: #fff;
      border-radius: 10px;
      max-width: 720px;
      width: 100%;
      padding: 20px 24px;
      box-shadow: 0 20px 40px rgba(2,6,23,0.12);
    }
    .modal-close {
      float: right;
      background: transparent;
      border: none;
      font-size: 20px;
      cursor: button;
    }
    .modal-row { margin: 8px 0; }
    .modal-row strong { display:inline-block; width:140px; }
  </style>
</head>
<body>
  <div class="container">

    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="profile">
        <div class="avatar"><?php echo htmlspecialchars($avatar); ?></div>
        <h2><?php echo htmlspecialchars($doctor_name); ?></h2>
      </div>
      <!-- Appointment Details Modal -->
      <div id="appointmentModal" class="modal" aria-hidden="true">
        <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
          <button class="modal-close" aria-label="Close">&times;</button>
          <h2 id="modalTitle">Appointment Details</h2>
          <div class="modal-row"><strong>Patient:</strong> <span id="m_patient"></span></div>
          <div class="modal-row"><strong>Email:</strong> <span id="m_email"></span></div>
          <div class="modal-row"><strong>Patient ID:</strong> <span id="m_pid"></span></div>
          <div class="modal-row"><strong>Date:</strong> <span id="m_date"></span></div>
          <div class="modal-row"><strong>Time:</strong> <span id="m_time"></span></div>
          <div class="modal-row"><strong>Purpose:</strong> <span id="m_purpose"></span></div>
          <div class="modal-row"><strong>Status:</strong> <span id="m_status"></span></div>
          <div style="margin-top:16px">
            <form id="modalAcceptForm" method="post" action="../../backend/doctor_backend/update_appointment_status.php" style="display:inline">
              <input type="hidden" name="appointment_id" id="modal_app_id_accept" value="">
              <input type="hidden" name="action" value="accept">
              <button type="submit" class="accept-btn btn-icon" title="Accept appointment">
                <span>Accept</span>
              </button>
            </form>
            <form id="modalDeclineForm" method="post" action="../../backend/doctor_backend/update_appointment_status.php" style="display:inline">
              <input type="hidden" name="appointment_id" id="modal_app_id_decline" value="">
              <input type="hidden" name="action" value="decline">
              <button type="submit" class="decline-btn btn-icon" title="Decline appointment">
                <span>Decline</span>
              </button>
            </form>
            <button id="modalBackBtn" class="secondary btn-icon" style="margin-left:8px" title="Close modal">
              <span>Close</span>
            </button>
          </div>
        </div>
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
        <?php if (!empty($appointments)): ?>
          <?php foreach ($appointments as $a): ?>
            <div class="appointment-card">
              <div class="card-info">
                <div>
                  <h2><?php echo htmlspecialchars($a['first_name'] . ' ' . $a['last_name']); ?></h2>
                  <p class="purpose"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $a['purpose']))); ?></p>
                </div>
                <?php $status = htmlspecialchars($a['status']); ?>
                <span class="status <?php echo $status === 'pending' ? 'pending' : 'confirmed'; ?>"><?php echo $status; ?></span>
              </div>
              <div class="card-details">
                <p><img src="../../assets/icons/doctorDashboard/date.svg" alt="date"> <?php echo htmlspecialchars($a['appt_date']); ?></p>
                <p><img src="../../assets/icons/doctorDashboard/time.svg" alt="time"><?php echo htmlspecialchars($a['appt_time']); ?></p>
                <p><img src="../../assets/icons/doctorDashboard/person.svg" alt="person">Patient ID: <?php echo htmlspecialchars($a['patient_id']); ?></p>
              </div>
              <div class="card-actions">
    <button class="view-btn" 
      data-id="<?php echo htmlspecialchars($a['id']); ?>" 
      data-patient-name="<?php echo htmlspecialchars($a['first_name'] . ' ' . $a['last_name']); ?>"
      data-email="<?php echo htmlspecialchars($a['email']); ?>"
      data-date="<?php echo htmlspecialchars($a['appt_date']); ?>"
      data-time="<?php echo htmlspecialchars($a['appt_time']); ?>"
      data-purpose="<?php echo htmlspecialchars($a['purpose']); ?>"
      data-status="<?php echo htmlspecialchars($a['status']); ?>"
      data-patient-id="<?php echo htmlspecialchars($a['patient_id']); ?>"
    >View Details</button>
    
                <?php
                  // Show inline accept/decline buttons beside View Details so doctor can act quickly.
                  $is_pending = ($a['status'] === 'pending');
                ?>
                <form method="post" action="../../backend/doctor_backend/update_appointment_status.php" style="display:inline">
                  <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($a['id']); ?>">
                  <input type="hidden" name="action" value="accept">
                  <button type="submit" class="accept-btn" <?php echo $is_pending ? '' : 'disabled title="Only available for pending appointments"'; ?>>Accept</button>
                </form>
                <form method="post" action="../../backend/doctor_backend/update_appointment_status.php" style="display:inline">
                  <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($a['id']); ?>">
                  <input type="hidden" name="action" value="decline">
                  <button type="submit" class="decline-btn" <?php echo $is_pending ? '' : 'disabled title="Only available for pending appointments"'; ?>>Decline</button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>No appointments found for you at the moment.</p>
        <?php endif; ?>
      </div>
    </main>
  </div>
  <script src="./doctor_JS/doctor_myAppointments.js"></script>
</body>
</html>
