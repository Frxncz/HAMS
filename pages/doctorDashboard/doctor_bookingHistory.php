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
      <?php
      // fetch appointments for this doctor
      require_once __DIR__ . '/../../backend/db_connect.php';
      $doctor_id = intval($_SESSION['user_id']);
      $rows = [];
      if ($stmt = $conn->prepare("SELECT a.id, a.appt_date, a.appt_time, a.purpose, a.status, p.pid, p.first_name, p.last_name, p.email FROM appointments a JOIN patient p ON a.patient_id = p.pid WHERE a.doctor_id = ? ORDER BY a.appt_date DESC, a.appt_time DESC")) {
        $stmt->bind_param('i', $doctor_id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        $stmt->close();
      }
      ?>

      <table>
        <thead>
          <tr>
            <th>Appointment #</th>
            <th>Patient Name</th>
            <th>Date</th>
            <th>Time</th>
            <th>Purpose</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($rows)): ?>
            <tr><td colspan="7">No bookings found.</td></tr>
          <?php else: ?>
            <?php foreach ($rows as $r): ?>
              <tr>
                <td>APT-<?php echo str_pad($r['id'],3,'0',STR_PAD_LEFT); ?></td>
                <td><?php echo htmlspecialchars($r['first_name'] . ' ' . $r['last_name']); ?></td>
                <td><?php echo htmlspecialchars($r['appt_date']); ?></td>
                <td><?php echo htmlspecialchars(substr($r['appt_time'],0,5)); ?></td>
                <td><?php echo htmlspecialchars(ucwords(str_replace('_',' ',$r['purpose']))); ?></td>
                <td><span class="status <?php echo htmlspecialchars($r['status']); ?>"><?php echo htmlspecialchars($r['status']); ?></span></td>
                <td>
                  <?php if ($r['status'] !== 'pending'): ?>
                    <button class="consult-btn" type="button"
                      data-id="<?php echo htmlspecialchars($r['id']); ?>"
                      data-patient="<?php echo htmlspecialchars($r['first_name'] . ' ' . $r['last_name']); ?>"
                      data-email="<?php echo htmlspecialchars($r['email']); ?>"
                      data-date="<?php echo htmlspecialchars($r['appt_date']); ?>"
                      data-time="<?php echo htmlspecialchars(substr($r['appt_time'],0,5)); ?>"
                      data-purpose="<?php echo htmlspecialchars($r['purpose']); ?>"
                      data-status="<?php echo htmlspecialchars($r['status']); ?>"
                    >View</button>
                  <?php else: ?>
                    -
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
      
      <!-- Consultation Details Modal -->
      <div id="consultModal" class="modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
        <div class="modal-box" style="background:#fff; padding:20px; border-radius:8px; max-width:680px; width:100%;">
          <button id="consultClose" style="float:right; background:none; border:none; font-size:20px;">&times;</button>
          <h3>Consultation Details</h3>
          <div><strong>Patient:</strong> <span id="c_patient"></span></div>
          <div><strong>Email:</strong> <span id="c_email"></span></div>
          <div><strong>Date:</strong> <span id="c_date"></span></div>
          <div><strong>Time:</strong> <span id="c_time"></span></div>
          <div><strong>Purpose:</strong> <span id="c_purpose"></span></div>
          <div><strong>Status:</strong> <span id="c_status"></span></div>
        </div>
      </div>
      <script>
        (function(){
          function openModal(data){
            document.getElementById('c_patient').textContent = data.patient || '';
            document.getElementById('c_email').textContent = data.email || '';
            document.getElementById('c_date').textContent = data.date || '';
            document.getElementById('c_time').textContent = data.time || '';
            document.getElementById('c_purpose').textContent = data.purpose || '';
            document.getElementById('c_status').textContent = data.status || '';
            var modal = document.getElementById('consultModal');
            modal.style.display = 'flex';
          }

          function closeModal(){
            var modal = document.getElementById('consultModal');
            modal.style.display = 'none';
          }

          document.querySelectorAll('.consult-btn').forEach(function(btn){
            btn.addEventListener('click', function(e){
              var el = e.currentTarget;
              var data = {
                id: el.getAttribute('data-id'),
                patient: el.getAttribute('data-patient'),
                email: el.getAttribute('data-email'),
                date: el.getAttribute('data-date'),
                time: el.getAttribute('data-time'),
                purpose: el.getAttribute('data-purpose'),
                status: el.getAttribute('data-status')
              };
              openModal(data);
            });
          });

          document.getElementById('consultClose').addEventListener('click', closeModal);
          // close when clicking outside box
          document.getElementById('consultModal').addEventListener('click', function(e){
            if (e.target === this) closeModal();
          });
        })();
      </script>
    </div>

  </div>
</body>
</html>
