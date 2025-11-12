<?php
require_once __DIR__ . '/../../backend/db_connect.php';

// fetch doctors with patient counts and status (handle missing `status` column gracefully)
$doctors = [];
$hasStatus = false;
$check = $conn->query("SHOW COLUMNS FROM doctor LIKE 'status'");
if ($check && $check->num_rows > 0) {
  $hasStatus = true;
}

if ($hasStatus) {
  $sql = "SELECT d.docid, d.name, d.email, d.specialty, COALESCE(d.status,'active') AS status, COALESCE(COUNT(DISTINCT a.patient_id),0) AS patients_count FROM doctor d LEFT JOIN appointments a ON a.doctor_id = d.docid GROUP BY d.docid ORDER BY d.name";
} else {
  // fallback: no status column, return 'active' as default
  $sql = "SELECT d.docid, d.name, d.email, d.specialty, 'active' AS status, COALESCE(COUNT(DISTINCT a.patient_id),0) AS patients_count FROM doctor d LEFT JOIN appointments a ON a.doctor_id = d.docid GROUP BY d.docid ORDER BY d.name";
}

if ($stmt = $conn->prepare($sql)) {
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
  <title>Admin view Doctors</title>
  <link rel="stylesheet" href="./admin_CSS/admin_viewDoctors.css" />
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
        <a href="./admin_viewDoctors.php" class="menu-item active">
          <img src="../../assets/icons/adminDashboard/viewDoctors.svg" alt="doctor icon">
          View Doctors
        </a>
        <a href="./admin_viewPatients.php" class="menu-item">
          <img src="../../assets/icons/adminDashboard/viewPatients.svg" alt="consultations icon">
          View Patients
        </a>
        <a href="./admin_appointments.php" class="menu-item">
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
        <div class="top-bar">
          <h1 class="page-title">Doctors Management</h1>
          <button id="addDoctorBtn" class="btn primary">+ Add New Doctor</button>
        </div>

        <input type="text" class="search-bar" placeholder="Search by name, email, or specialty...">

        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Specialty</th>
              <th>Phone</th>
              <th>Patients</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($doctors)): ?>
              <?php foreach ($doctors as $d): ?>
                <tr>
                  <td><?php echo htmlspecialchars($d['name']); ?></td>
                  <td><?php echo htmlspecialchars($d['email']); ?></td>
                  <td><?php echo htmlspecialchars($d['specialty']); ?></td>
                  <td>&mdash;</td>
                  <td><?php echo htmlspecialchars($d['patients_count']); ?></td>
                  <td><span class="status"><?php echo htmlspecialchars($d['status']); ?></span></td>
                  <td>
                    <button class="btn view view-doctor" data-doc='<?php echo json_encode($d, JSON_HEX_APOS|JSON_HEX_QUOT); ?>'>View</button>
                    <button class="btn edit edit-doctor" data-doc='<?php echo json_encode($d, JSON_HEX_APOS|JSON_HEX_QUOT); ?>'>Edit</button>
                    <?php if ($d['status'] === 'inactive'): ?>
                      <form style="display:inline" method="post" action="../../backend/admin_backend/update_doctor_status.php">
                        <input type="hidden" name="docid" value="<?php echo htmlspecialchars($d['docid']); ?>">
                        <input type="hidden" name="action" value="activate">
                        <button class="btn activate">Activate</button>
                      </form>
                    <?php else: ?>
                      <form style="display:inline" method="post" action="../../backend/admin_backend/update_doctor_status.php">
                        <input type="hidden" name="docid" value="<?php echo htmlspecialchars($d['docid']); ?>">
                        <input type="hidden" name="action" value="deactivate">
                        <button class="btn deactivate">Deactivate</button>
                      </form>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="7">No doctors found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- View Modal -->
      <div id="doctorViewModal" class="modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
        <div class="modal-box" style="background:#fff; padding:20px; border-radius:8px; max-width:600px; width:100%;">
          <button id="doctorViewClose" style="float:right; background:none; border:none; font-size:20px;">&times;</button>
          <h3>Doctor Details</h3>
          <div><strong>Name:</strong> <span id="v_name"></span></div>
          <div><strong>Email:</strong> <span id="v_email"></span></div>
          <div><strong>Specialty:</strong> <span id="v_specialty"></span></div>
          <div><strong>Patients:</strong> <span id="v_patients"></span></div>
        </div>
      </div>

      <!-- Edit Modal -->
      <div id="doctorEditModal" class="modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
        <div class="modal-box" style="background:#fff; padding:20px; border-radius:8px; max-width:600px; width:100%;">
          <button id="doctorEditClose" style="float:right; background:none; border:none; font-size:20px;">&times;</button>
          <h3>Edit Doctor</h3>
          <form id="doctorEditForm" method="post" action="../../backend/admin_backend/update_doctor.php">
            <input type="hidden" name="docid" id="e_docid">
            <label>Name</label>
            <input type="text" name="name" id="e_name" required>
            <label>Email</label>
            <input type="email" name="email" id="e_email" required>
            <label>Specialty</label>
            <input type="text" name="specialty" id="e_specialty">
            <label>Password (leave blank to keep current)</label>
            <input type="password" name="password" id="e_password">
            <div style="margin-top:12px;">
              <button type="submit" class="btn primary">Save</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Create Modal -->
      <div id="doctorCreateModal" class="modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
        <div class="modal-box" style="background:#fff; padding:20px; border-radius:8px; max-width:600px; width:100%;">
          <button id="doctorCreateClose" style="float:right; background:none; border:none; font-size:20px;">&times;</button>
          <h3>Add New Doctor</h3>
          <form id="doctorCreateForm" method="post" action="../../backend/admin_backend/create_doctor.php">
            <label>Name</label>
            <input type="text" name="name" id="c_name" required>
            <label>Email</label>
            <input type="email" name="email" id="c_email" required>
            <label>Specialty</label>
            <input type="text" name="specialty" id="c_specialty">
            <label>Password</label>
            <input type="password" name="password" id="c_password" required>
            <div style="margin-top:12px;">
              <button type="submit" class="btn primary">Create</button>
            </div>
          </form>
        </div>
      </div>

      <script>
        (function(){
          function openView(doc){
            document.getElementById('v_name').textContent = doc.name || '';
            document.getElementById('v_email').textContent = doc.email || '';
            document.getElementById('v_specialty').textContent = doc.specialty || '';
            document.getElementById('v_patients').textContent = doc.patients_count || '0';
            document.getElementById('doctorViewModal').style.display = 'flex';
          }

          function openEdit(doc){
            document.getElementById('e_docid').value = doc.docid || '';
            document.getElementById('e_name').value = doc.name || '';
            document.getElementById('e_email').value = doc.email || '';
            document.getElementById('e_specialty').value = doc.specialty || '';
            document.getElementById('e_password').value = '';
            document.getElementById('doctorEditModal').style.display = 'flex';
          }

          document.querySelectorAll('.view-doctor').forEach(function(btn){
            btn.addEventListener('click', function(){
              var doc = JSON.parse(this.getAttribute('data-doc') || '{}');
              openView(doc);
            });
          });

          document.querySelectorAll('.edit-doctor').forEach(function(btn){
            btn.addEventListener('click', function(){
              var doc = JSON.parse(this.getAttribute('data-doc') || '{}');
              openEdit(doc);
            });
          });

          document.getElementById('doctorViewClose').addEventListener('click', function(){ document.getElementById('doctorViewModal').style.display='none'; });
          document.getElementById('doctorEditClose').addEventListener('click', function(){ document.getElementById('doctorEditModal').style.display='none'; });
          document.getElementById('doctorCreateClose').addEventListener('click', function(){ document.getElementById('doctorCreateModal').style.display='none'; });
          document.getElementById('addDoctorBtn').addEventListener('click', function(){ document.getElementById('doctorCreateModal').style.display='flex'; });
          // close on outside click
          document.getElementById('doctorViewModal').addEventListener('click', function(e){ if (e.target===this) this.style.display='none'; });
          document.getElementById('doctorEditModal').addEventListener('click', function(e){ if (e.target===this) this.style.display='none'; });
          document.getElementById('doctorCreateModal').addEventListener('click', function(e){ if (e.target===this) this.style.display='none'; });
        })();
      </script>
</body>
</html>
