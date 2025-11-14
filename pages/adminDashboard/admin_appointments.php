<?php
require_once __DIR__ . '/../../backend/db_connect.php';
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../../login.html');
    exit;
}

// detect optional columns (e.g., type)
$colsRes = $conn->query("SHOW COLUMNS FROM appointments");
$cols = [];
if ($colsRes) {
    while ($c = $colsRes->fetch_assoc()) {
        $cols[] = $c['Field'];
    }
}
$hasType = in_array('type', $cols, true);

$typeSelect = $hasType ? "COALESCE(a.type, 'In-Person') AS appt_type" : "'In-Person' AS appt_type";

// fetch all appointments with patient and doctor
$sql = "SELECT a.id, a.appt_date, a.appt_time, a.purpose, a.status, p.pid AS patient_id, p.first_name AS patient_first, p.last_name AS patient_last, p.email AS patient_email, d.docid AS doctor_id, d.name AS doctor_name, $typeSelect
        FROM appointments a
        LEFT JOIN patient p ON a.patient_id = p.pid
        LEFT JOIN doctor d ON a.doctor_id = d.docid
        ORDER BY a.appt_date DESC, a.appt_time DESC";
$res = $conn->query($sql);
$appointments = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $appointments[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Appointments</title>
  <link rel="stylesheet" href="./admin_CSS/admin_appointments.css" />
  <link rel="stylesheet" href="../../css/global.css" />
  <link rel="stylesheet" href="../../css/sidebar.css" />
</head>
<body>
  <div class="container">

    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="profile">
        <div class="avatar">A</div>
        <h2>Administrator</h2>
      </div>

      <button class="logout-btn" onclick="window.location.href='../../backend/logout.php'">
        <img src="../../assets/icons/patientsDashboard/logout.svg" alt="logout icon">
        Log out
      </button>

      <nav class="menu">
        <a href="./admin_dashboard.php" class="menu-item">
          <img src="../../assets/icons/adminDashboard/adminDashboard.svg" alt="home icon">
          Dashboard
        </a>
        <a href="./admin_viewDoctors.php" class="menu-item">
          <img src="../../assets/icons/adminDashboard/viewDoctors.svg" alt="doctor icon">
          View Doctors
        </a>
        <a href="./admin_viewPatients.php" class="menu-item">
          <img src="../../assets/icons/adminDashboard/viewPatients.svg" alt="consultations icon">
          View Patients
        </a>
        <a href="./admin_appointments.php" class="menu-item active">
          <img src="../../assets/icons/adminDashboard/appointments.svg" alt="history icon">
          Appointments
        </a>
        <a href="./admin_settings.php" class="menu-item">
          <img src="../../assets/icons/adminDashboard/settings.svg" alt="settings icon">
          Account Settings
        </a>
      </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
      <h1 class="page-title">Appointments</h1>

      <div class="filters">
        <input type="text" class="search-bar" placeholder="Search by patient name or appointment number...">
        <select class="filter-select">
          <option>All Doctors</option>
          <option>Dr. Sarah Johnson</option>
          <option>Dr. Michael Chen</option>
          <option>Dr. Emily Williams</option>
        </select>
        <select class="filter-select">
          <option>All Statuses</option>
          <option>Confirmed</option>
          <option>Pending</option>
          <option>Cancelled</option>
        </select>
      </div>

      <table>
        <thead>
          <tr>
            <th>Appointment #</th>
            <th>Patient Name</th>
            <th>Doctor</th>
            <th>Date</th>
            <th>Time</th>
            <th>Type</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($appointments)): foreach ($appointments as $a):
            $aid = (int)$a['id'];
            $apptNo = 'APT-' . str_pad($aid, 3, '0', STR_PAD_LEFT);
            $patientName = htmlspecialchars(($a['patient_first'] ?? '') . ' ' . ($a['patient_last'] ?? ''));
            $doctorName = htmlspecialchars($a['doctor_name'] ?? '');
            $date = htmlspecialchars($a['appt_date']);
            $time = htmlspecialchars($a['appt_time']);
            $type = htmlspecialchars($a['appt_type'] ?? 'In-Person');
            $status = htmlspecialchars($a['status'] ?? 'pending');
          ?>
          <tr id="appt-row-<?php echo $aid; ?>" data-id="<?php echo $aid; ?>" data-patient="<?php echo $patientName; ?>" data-patient-email="<?php echo htmlspecialchars($a['patient_email'] ?? ''); ?>" data-doctor="<?php echo $doctorName; ?>" data-date="<?php echo $date; ?>" data-time="<?php echo $time; ?>" data-type="<?php echo $type; ?>" data-purpose="<?php echo htmlspecialchars($a['purpose'] ?? ''); ?>" data-status="<?php echo $status; ?>">
            <td><?php echo $apptNo; ?></td>
            <td><?php echo $patientName; ?></td>
            <td><?php echo $doctorName; ?></td>
            <td><?php echo $date; ?></td>
            <td><?php echo $time; ?></td>
            <td><?php echo $type; ?></td>
            <td><span class="status <?php echo $status; ?>"><?php echo $status; ?></span></td>
            <td><button class="btn view-details" type="button" data-id="<?php echo $aid; ?>">View Details</button></td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="8">No appointments found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
      
      <!-- Appointment Details Modal -->
      <div id="apptModal" class="modal" style="display:none;">
        <div class="modal-content">
          <span class="close" onclick="document.getElementById('apptModal').style.display='none'">&times;</span>
          <h2>Appointment Details</h2>
          <p><strong>Appointment #:</strong> <span id="m_appt_no"></span></p>
          <p><strong>Patient:</strong> <span id="m_patient_name"></span></p>
          <p><strong>Patient Email:</strong> <span id="m_patient_email"></span></p>
          <p><strong>Doctor:</strong> <span id="m_doctor"></span></p>
          <p><strong>Date:</strong> <span id="m_date"></span></p>
          <p><strong>Time:</strong> <span id="m_time"></span></p>
          <p><strong>Type:</strong> <span id="m_type"></span></p>
          <p><strong>Purpose:</strong> <span id="m_purpose"></span></p>
          <p><strong>Status:</strong> <span id="m_status"></span></p>
        </div>
      </div>
    </div>

  </div>
  <script>
    // View details modal handler
    document.addEventListener('click', function(e) {
      if (e.target && e.target.matches('.view-details')) {
        const id = e.target.dataset.id;
        const row = document.getElementById('appt-row-' + id);
        if (!row) return alert('Appointment data not found');
        document.getElementById('m_appt_no').textContent = 'APT-' + String(id).padStart(3,'0');
        document.getElementById('m_patient_name').textContent = row.dataset.patient || '';
        document.getElementById('m_patient_email').textContent = row.dataset.patientEmail || '';
        document.getElementById('m_doctor').textContent = row.dataset.doctor || '';
        document.getElementById('m_date').textContent = row.dataset.date || '';
        document.getElementById('m_time').textContent = row.dataset.time || '';
        document.getElementById('m_type').textContent = row.dataset.type || '';
        document.getElementById('m_purpose').textContent = row.dataset.purpose || '';
        document.getElementById('m_status').textContent = row.dataset.status || '';
        document.getElementById('apptModal').style.display = 'flex';
      }
    });
    // close modal when clicking outside
    window.addEventListener('click', function(e){
      const modal = document.getElementById('apptModal');
      if (!modal) return;
      if (e.target === modal) modal.style.display = 'none';
    });
  </script>
</body>
</html>
