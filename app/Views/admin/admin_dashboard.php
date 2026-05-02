<?php
// Admin dashboard counts: total doctors, total appointments, appointments today, patients with appointments today
require_once __DIR__ . '/../../Models/db_connect.php';
require_once __DIR__ . '/../partials/dashboard_sidebar.php';

$totalDoctors = 0;
$totalAppointments = 0;
$appointmentsToday = 0;
$patientsToday = 0;
$totalPatients = 0;

// Total doctors
if ($stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM doctor")) {
  $stmt->execute();
  $res = $stmt->get_result();
  $row = $res->fetch_assoc();
  $totalDoctors = intval($row['cnt'] ?? 0);
  $stmt->close();
}

// Total appointments
if ($stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM appointments")) {
  $stmt->execute();
  $res = $stmt->get_result();
  $row = $res->fetch_assoc();
  $totalAppointments = intval($row['cnt'] ?? 0);
  $stmt->close();
}

$today = date('Y-m-d');

// Appointments today
if ($stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM appointments WHERE appt_date = ?")) {
  $stmt->bind_param('s', $today);
  $stmt->execute();
  $res = $stmt->get_result();
  $row = $res->fetch_assoc();
  $appointmentsToday = intval($row['cnt'] ?? 0);
  $stmt->close();
}

// Patients (distinct) with appointments today
if ($stmt = $conn->prepare("SELECT COUNT(DISTINCT patient_id) AS cnt FROM appointments WHERE appt_date = ?")) {
  $stmt->bind_param('s', $today);
  $stmt->execute();
  $res = $stmt->get_result();
  $row = $res->fetch_assoc();
  $patientsToday = intval($row['cnt'] ?? 0);
  $stmt->close();
}

// Total distinct patient accounts
if ($stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM patient")) {
  $stmt->execute();
  $res = $stmt->get_result();
  $row = $res->fetch_assoc();
  $totalPatients = intval($row['cnt'] ?? 0);
  $stmt->close();
}

// Fetch all patients with visit counts
$patients = [];
if ($stmt = $conn->prepare("SELECT p.pid, p.first_name, p.last_name, p.email, COALESCE(COUNT(a.id),0) AS visits FROM patient p LEFT JOIN appointments a ON a.patient_id = p.pid GROUP BY p.pid ORDER BY p.last_name, p.first_name")) {
  $stmt->execute();
  $res = $stmt->get_result();
  while ($r = $res->fetch_assoc()) $patients[] = $r;
  $stmt->close();
}

// Fetch all doctors with distinct patient counts
$doctors = [];
if ($stmt = $conn->prepare("SELECT d.docid, d.name, d.email, d.specialty, COALESCE(COUNT(DISTINCT a.patient_id),0) AS patients_count FROM doctor d LEFT JOIN appointments a ON a.doctor_id = d.docid GROUP BY d.docid ORDER BY d.name")) {
  $stmt->execute();
  $res = $stmt->get_result();
  while ($r = $res->fetch_assoc()) $doctors[] = $r;
  $stmt->close();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="/HAMS/public/css/admin/admin_dashboard.css" />
  <link rel="stylesheet" href="/HAMS/public/css/global.css" />
  <link rel="stylesheet" href="/HAMS/public/css/sidebar.css" />
</head>
<body>
  <div class="container">
    <?php render_dashboard_sidebar([
      'role' => 'admin',
      'active' => 'dashboard',
      'name' => 'Administrator',
      'avatar' => 'A',
    ]); ?>

    <!-- Main Content -->

    <div class="main-content">
      <h1 class="page-title">Dashboard</h1>

      <!-- Stats Cards -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="icon-circle blue">
            <img src="/HAMS/public/assets/icons/adminDashboard/stat_person.svg" alt="Total Patients icon">
          </div>
          <div class="stat-label">Total Patients</div>
          <div class="stat-value"><?php echo htmlspecialchars($totalPatients); ?></div>
        </div>

        <div class="stat-card">
          <div class="icon-circle green">
            <img src="/HAMS/public/assets/icons/adminDashboard/stat_doctors.svg" alt="Total Doctors icon">
          </div>
          <div class="stat-label">Total Doctors</div>
          <div class="stat-value"><?php echo htmlspecialchars($totalDoctors); ?></div>
        </div>

        <div class="stat-card">
          <div class="icon-circle purple">
            <img src="/HAMS/public/assets/icons/adminDashboard/stat_appointments.svg" alt="Total Appointments icon">
          </div>
          <div class="stat-label">Total Appointments</div>
          <div class="stat-value"><?php echo htmlspecialchars($totalAppointments); ?></div>
        </div>

        <div class="stat-card">
          <div class="icon-circle orange">
            <img src="/HAMS/public/assets/icons/adminDashboard/stat_date.svg" alt="Appointments Today icon">
          </div>
          <div class="stat-label">Appointments Today</div>
          <div class="stat-value"><?php echo htmlspecialchars($appointmentsToday); ?></div>
        </div>
      </div>

      <!-- All Patients Section -->
      <div class="section">
        <div class="section-header">
          <img src="/HAMS/public/assets/icons/adminDashboard/admin_allPatients.svg" alt="All Patients icon">
          All Patients
        </div>

        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Total Visits</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($patients)): ?>
              <?php foreach ($patients as $p): ?>
                <tr>
                  <td><?php echo htmlspecialchars($p['first_name'] . ' ' . $p['last_name']); ?></td>
                  <td><?php echo htmlspecialchars($p['email']); ?></td>
                  <td><?php echo htmlspecialchars($p['visits']); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="3">No patients found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- All Doctors Section -->
      <div class="section">
        <div class="section-header">
          <img src="/HAMS/public/assets/icons/adminDashboard/admin_allDoctors.svg" alt="All Doctors icon">
          All Doctors
        </div>

        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Specialty</th>
              <th>Patients</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($doctors)): ?>
              <?php foreach ($doctors as $d): ?>
                <tr>
                  <td><?php echo htmlspecialchars($d['name']); ?></td>
                  <td><?php echo htmlspecialchars($d['email']); ?></td>
                  <td><?php echo htmlspecialchars($d['specialty']); ?></td>
                  <td><?php echo htmlspecialchars($d['patients_count']); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="4">No doctors found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>



