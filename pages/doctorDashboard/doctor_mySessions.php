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
  <title>Doctor My </title>
  <link rel="stylesheet" href="./doctor_CSS/doctor_mySession.css" />
  <link rel="stylesheet" href="../../css/global.css" />
  <link rel="stylesheet" href="../../css/sidebar.css" />
  <style>
    /* modal styles (shared) */
    .modal { display: none; position: fixed; z-index:2000; left:0; top:0; width:100%; height:100%; background: rgba(0,0,0,0.5); align-items:center; justify-content:center; padding:20px; }
    .modal.open { display:flex; }
    .modal-box { background:#fff; border-radius:10px; max-width:720px; width:100%; padding:20px; box-shadow:0 20px 40px rgba(2,6,23,0.12); }
    .modal-close { float:right; background:transparent; border:none; font-size:20px; cursor:pointer; }
    .modal-row { margin:8px 0; }
    .modal-row strong { display:inline-block; width:140px; }
    .form-row { display:flex; gap:8px; align-items:center; }
    .form-row input[type="date"], .form-row input[type="time"] { padding:8px; border:1px solid #e5e7eb; border-radius:6px; }
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
        <a href="./doctor_mySessions.php" class="menu-item active">
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

    <!-- Main Content-->
      <div class="main-content">
        <h1 class="page-title">My Sessions</h1>

        <?php
        // include DB connection and fetch confirmed appointments for this doctor
        require_once __DIR__ . '/../../backend/db_connect.php';
        $doctor_id = intval($_SESSION['user_id']);
        $sessions = [];
        if ($stmt = $conn->prepare("SELECT a.id, a.appt_date, a.appt_time, a.purpose, a.status, p.pid AS patient_id, p.first_name, p.last_name, p.email FROM appointments a JOIN patient p ON a.patient_id = p.pid WHERE a.doctor_id = ? AND a.status = 'confirmed' ORDER BY a.appt_date ASC, a.appt_time ASC")) {
          $stmt->bind_param('i', $doctor_id);
          $stmt->execute();
          $res = $stmt->get_result();
          while ($row = $res->fetch_assoc()) { $sessions[] = $row; }
          $stmt->close();
        }
        ?>

        <div class="sessions-list">
          <?php if (!empty($sessions)): ?>
            <?php foreach ($sessions as $s): ?>
              <div class="session-card">
                <div class="card-header">
                  <div class="patient-info">
                    <h3><?php echo htmlspecialchars($s['first_name'] . ' ' . $s['last_name']); ?></h3>
                    <p><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $s['purpose']))); ?></p>
                  </div>
                  <span class="status-badge confirmed"><?php echo htmlspecialchars($s['status']); ?></span>
                </div>

                <div class="session-details">
                  <div class="detail-item">
                    <img src="../../assets/icons/patientsDashboard/date.svg" alt="calendar icon">
                    <span><?php echo htmlspecialchars($s['appt_date']); ?></span>
                  </div>

                  <div class="detail-item">
                    <img src="../../assets/icons/patientsDashboard/time.svg" alt="clock icon">
                    <span><?php echo htmlspecialchars($s['appt_time']); ?></span>
                  </div>

                  <div class="detail-item">
                    <img src="../../assets/icons/patientsDashboard/location.svg" alt="location icon">
                    <span>In-Person</span>
                  </div>
                  <!-- View Details Modal -->
                  <div id="sessionViewModal" class="modal" aria-hidden="true">
                    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="vmTitle">
                      <button class="modal-close" aria-label="Close">&times;</button>
                      <h2 id="vmTitle">Session Details</h2>
                      <div class="modal-row"><strong>Patient:</strong> <span id="sv_patient"></span></div>
                      <div class="modal-row"><strong>Email:</strong> <span id="sv_email"></span></div>
                      <div class="modal-row"><strong>Patient ID:</strong> <span id="sv_pid"></span></div>
                      <div class="modal-row"><strong>Date:</strong> <span id="sv_date"></span></div>
                      <div class="modal-row"><strong>Time:</strong> <span id="sv_time"></span></div>
                      <div class="modal-row"><strong>Purpose:</strong> <span id="sv_purpose"></span></div>
                      <div style="margin-top:12px"><button class="btn" onclick="document.getElementById('sessionViewModal').classList.remove('open')">Close</button></div>
                    </div>
                  </div>

                  <!-- Reschedule Modal -->
                  <div id="sessionReschedModal" class="modal" aria-hidden="true">
                    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="rmTitle">
                      <button class="modal-close" aria-label="Close">&times;</button>
                      <h2 id="rmTitle">Reschedule Session</h2>
                      <form method="post" action="../../backend/doctor_backend/reschedule_appointment.php">
                        <input type="hidden" name="appointment_id" id="rs_app_id" value="">
                        <div class="form-row">
                          <label for="rs_date"><strong>New date</strong></label>
                          <input type="date" name="new_date" id="rs_date" required>
                          <label for="rs_time"><strong>New time</strong></label>
                          <input type="time" name="new_time" id="rs_time" required>
                        </div>
                        <div style="margin-top:12px">
                          <button type="submit" class="btn btn-save">Save</button>
                          <button type="button" class="btn" onclick="document.getElementById('sessionReschedModal').classList.remove('open')">Cancel</button>
                        </div>
                      </form>
                    </div>
                  </div>

                  <script src="./doctor_JS/doctor_mySessions.js"></script>
                </div>

                <div class="session-actions">
                  <button class="btn btn-view" 
                          data-id="<?php echo htmlspecialchars($s['id']); ?>"
                          data-patient-name="<?php echo htmlspecialchars($s['first_name'] . ' ' . $s['last_name']); ?>"
                          data-email="<?php echo htmlspecialchars($s['email']); ?>"
                          data-date="<?php echo htmlspecialchars($s['appt_date']); ?>"
                          data-time="<?php echo htmlspecialchars($s['appt_time']); ?>"
                          data-purpose="<?php echo htmlspecialchars($s['purpose']); ?>"
                          data-pid="<?php echo htmlspecialchars($s['patient_id']); ?>"
                  >View Details</button>

                  <button class="btn btn-reschedule"
                          data-id="<?php echo htmlspecialchars($s['id']); ?>"
                          data-date="<?php echo htmlspecialchars($s['appt_date']); ?>"
                          data-time="<?php echo htmlspecialchars($s['appt_time']); ?>"
                  >Reschedule</button>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p>No confirmed sessions found.</p>
          <?php endif; ?>
        </div>
      </div>

  </div>
</body>
</html>
