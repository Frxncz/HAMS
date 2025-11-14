<?php
require_once __DIR__ . '/../../backend/db_connect.php';
session_start();
// simple admin check
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
  header('Location: ../../login.html');
  exit;
}

// Detect patient table columns to avoid unknown-column SQL errors
$colsRes = $conn->query("SHOW COLUMNS FROM patient");
$cols = [];
if ($colsRes) {
  while ($c = $colsRes->fetch_assoc()) {
    $cols[] = $c['Field'];
  }
}
$hasPhone = in_array('phone', $cols, true);
$hasAge = in_array('age', $cols, true);
$hasStatus = in_array('status', $cols, true);

$phoneSelect = $hasPhone ? "COALESCE(p.phone, '') AS phone" : "'' AS phone";
$ageSelect = $hasAge ? "COALESCE(p.age, '') AS age" : "'' AS age";
$statusSelect = $hasStatus ? "COALESCE(p.status, 'active') AS status" : "'active' AS status";

$sql = "SELECT p.pid, p.first_name, p.last_name, p.email, 
         $phoneSelect, $ageSelect, 
         $statusSelect, 
         COUNT(a.id) AS total_visits
    FROM patient p
    LEFT JOIN appointments a ON a.patient_id = p.pid
    GROUP BY p.pid
    ORDER BY p.first_name, p.last_name";

$res = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin View Patients</title>
  <link rel="stylesheet" href="./admin_CSS/admin_viewPatients.css" />
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
        <a href="./admin_viewPatients.php" class="menu-item active">
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
        <h1 class="page-title">Patients</h1>

        <input type="text" class="search-bar" placeholder="Search by name, email, or phone..." id="searchBar">

        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Age</th>
              <th>Total Visits</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="patientsTable">
            <?php if ($res && $res->num_rows > 0):
                while ($row = $res->fetch_assoc()):
                    $pid = (int)$row['pid'];
                    $first = htmlspecialchars($row['first_name']);
                    $last = htmlspecialchars($row['last_name']);
                    $email = htmlspecialchars($row['email']);
                    $phone = htmlspecialchars($row['phone']);
                    $age = htmlspecialchars($row['age']);
                    $status = htmlspecialchars($row['status']);
                    $visits = (int)$row['total_visits'];
                    $fullName = trim($first . ' ' . $last);
            ?>
            <tr id="patient-row-<?php echo $pid; ?>" data-pid="<?php echo $pid; ?>" data-first="<?php echo $first; ?>" data-last="<?php echo $last; ?>" data-email="<?php echo $email; ?>" data-phone="<?php echo $phone; ?>" data-age="<?php echo $age; ?>" data-status="<?php echo $status; ?>" data-visits="<?php echo $visits; ?>">
              <td class="p-name"><?php echo $fullName; ?></td>
              <td class="p-email"><?php echo $email; ?></td>
              <td class="p-phone"><?php echo $phone; ?></td>
              <td class="p-age"><?php echo $age; ?></td>
              <td class="p-visits"><?php echo $visits; ?></td>
              <td class="p-status"><span class="status <?php echo $status === 'active' ? 'active' : 'inactive'; ?>"><?php echo ucfirst($status); ?></span></td>
              <td>
                <div class="actions">
                  <button class="btn view" type="button" onclick="openView(<?php echo $pid; ?>)">View</button>
                  <button class="btn edit" type="button" onclick="openEdit(<?php echo $pid; ?>)">Edit</button>
                  <form class="status-form" method="post" style="display:inline-block; margin:0;" onsubmit="return false;">
                    <input type="hidden" name="pid" value="<?php echo $pid; ?>">
                    <input type="hidden" name="action" value="<?php echo $status === 'active' ? 'deactivate' : 'activate'; ?>">
                    <button class="btn toggle-status <?php echo $status === 'active' ? 'active' : 'inactive'; ?>" type="submit"><?php echo $status === 'active' ? 'Deactivate' : 'Activate'; ?></button>
                  </form>
                </div>
              </td>
            </tr>
            <?php endwhile; endif; ?>
          </tbody>
        </table>
     </div>

  </div>

  <!-- View Modal -->
  <div id="patientViewModal" class="modal" style="display:none;">
    <div class="modal-content">
      <span class="close" onclick="closeModal('patientViewModal')">&times;</span>
      <h2>Patient Details</h2>
      <p><strong>Name:</strong> <span id="viewName"></span></p>
      <p><strong>Email:</strong> <span id="viewEmail"></span></p>
      <p><strong>Phone:</strong> <span id="viewPhone"></span></p>
      <p><strong>Age:</strong> <span id="viewAge"></span></p>
      <p><strong>Total Visits:</strong> <span id="viewVisits"></span></p>
      <p><strong>Status:</strong> <span id="viewStatus"></span></p>
    </div>
  </div>

  <!-- Edit Modal -->
  <div id="patientEditModal" class="modal" style="display:none;">
    <div class="modal-content">
      <span class="close" onclick="closeModal('patientEditModal')">&times;</span>
      <h2>Edit Patient</h2>
      <form id="editPatientForm" method="post" onsubmit="return false;">
        <input type="hidden" name="pid" id="editPid">
        <div>
          <label>First name</label>
          <input type="text" name="first_name" id="editFirst" required>
        </div>
        <div>
          <label>Last name</label>
          <input type="text" name="last_name" id="editLast" required>
        </div>
        <div>
          <label>Email</label>
          <input type="email" name="email" id="editEmail" required>
        </div>
        <div>
          <label>Phone</label>
          <input type="text" name="phone" id="editPhone">
        </div>
        <div>
          <label>Age</label>
          <input type="number" name="age" id="editAge" min="0">
        </div>
        <div style="margin-top:12px;">
          <button class="btn primary" type="submit">Save</button>
          <button class="btn" type="button" onclick="closeModal('patientEditModal')">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const BASE = '/HAMS/backend/admin_backend';

    function closeModal(id) {
      document.getElementById(id).style.display = 'none';
    }

    function openView(pid) {
      const row = document.getElementById('patient-row-' + pid);
      if (!row) return alert('Row not found');
      document.getElementById('viewName').textContent = row.dataset.first + ' ' + row.dataset.last;
      document.getElementById('viewEmail').textContent = row.dataset.email;
      document.getElementById('viewPhone').textContent = row.dataset.phone;
      document.getElementById('viewAge').textContent = row.dataset.age;
      document.getElementById('viewVisits').textContent = row.dataset.visits;
      document.getElementById('viewStatus').textContent = row.dataset.status;
      document.getElementById('patientViewModal').style.display = 'block';
    }

    function openEdit(pid) {
      const row = document.getElementById('patient-row-' + pid);
      if (!row) return alert('Row not found');
      document.getElementById('editPid').value = pid;
      document.getElementById('editFirst').value = row.dataset.first;
      document.getElementById('editLast').value = row.dataset.last;
      document.getElementById('editEmail').value = row.dataset.email;
      document.getElementById('editPhone').value = row.dataset.phone;
      document.getElementById('editAge').value = row.dataset.age;
      document.getElementById('patientEditModal').style.display = 'block';
    }

    // status form handling
    document.addEventListener('click', function(e) {
        if (e.target && e.target.matches('.toggle-status, .toggle-status *')) {
        // ensure we get the button even if inner element was clicked
        const btn = e.target.classList && e.target.classList.contains('toggle-status') ? e.target : e.target.closest('.toggle-status');
        const form = btn ? btn.closest('.status-form') : e.target.closest('.status-form');
        if (!form) return;
        const pid = form.querySelector('input[name="pid"]').value;
        const actionInput = form.querySelector('input[name="action"]');
        const action = actionInput.value;
        if (action === 'deactivate' && !confirm('Are you sure you want to deactivate this patient?')) return;

        const data = new FormData();
        data.append('pid', pid);
        data.append('action', action);

        fetch(BASE + '/update_patient_status.php', {
          method: 'POST',
          body: data,
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).then(r => r.text())
        .then(text => {
          let json = null;
          try { json = JSON.parse(text); } catch (err) { json = null; }
          if (json && json.success) {
            const row = document.getElementById('patient-row-' + pid);
            const newStatus = json.status;
            row.dataset.status = newStatus;
            // update hidden action for next click
            actionInput.value = newStatus === 'active' ? 'deactivate' : 'activate';
            // update the toggle button text and classes
            if (btn) {
              btn.textContent = newStatus === 'active' ? 'Deactivate' : 'Activate';
              btn.classList.remove('active','inactive');
              btn.classList.add(newStatus === 'active' ? 'active' : 'inactive');
            }
            // update status badge in the row
            const badge = row.querySelector('.p-status .status');
            if (badge) {
              badge.textContent = newStatus;
              badge.classList.remove('active','inactive');
              badge.classList.add(newStatus === 'active' ? 'active' : 'inactive');
            }
            // also update viewStatus if modal open
            const viewStatus = document.getElementById('viewStatus');
            if (viewStatus) viewStatus.textContent = newStatus;
            // keep layout stable - no element replacement
          } else {
            const errMsg = json && json.error ? json.error : (text || 'Unknown error');
            alert('Failed to update status: ' + (errMsg.substring ? errMsg.substring(0,300) : errMsg));
            console.error('Status update response:', text);
          }
        }).catch(err => {
          console.error(err);
          alert('Network or server error while updating status.');
        });
      }
    });

    // edit form submit via AJAX
    document.getElementById('editPatientForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const form = e.target;
      const data = new FormData(form);
      fetch(BASE + '/update_patient.php', {
        method: 'POST',
        body: data,
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
      }).then(r => r.text())
      .then(text => {
        let json = null;
        try { json = JSON.parse(text); } catch (err) { json = null; }
        if (json && json.success) {
          const pid = form.querySelector('input[name="pid"]').value;
          const row = document.getElementById('patient-row-' + pid);
          // update row data and visible cells
          row.dataset.first = document.getElementById('editFirst').value;
          row.dataset.last = document.getElementById('editLast').value;
          row.dataset.email = document.getElementById('editEmail').value;
          row.dataset.phone = document.getElementById('editPhone').value;
          row.dataset.age = document.getElementById('editAge').value;
          row.querySelector('.p-name').textContent = row.dataset.first + ' ' + row.dataset.last;
          row.querySelector('.p-email').textContent = row.dataset.email;
          row.querySelector('.p-phone').textContent = row.dataset.phone;
          row.querySelector('.p-age').textContent = row.dataset.age;
          closeModal('patientEditModal');
          alert('Patient updated');
        } else {
          const errMsg = json && json.error ? json.error : (text || 'Unknown error');
          alert('Failed to update patient: ' + (errMsg.substring ? errMsg.substring(0,300) : errMsg));
          console.error('Update response:', text);
        }
      }).catch(err => {
        console.error(err);
        alert('Network or server error while updating patient.');
      });
    });

    // simple client-side search
    document.getElementById('searchBar').addEventListener('input', function(e) {
      const q = e.target.value.toLowerCase();
      document.querySelectorAll('#patientsTable tr').forEach(row => {
        const text = (row.dataset.first + ' ' + row.dataset.last + ' ' + row.dataset.email + ' ' + row.dataset.phone).toLowerCase();
        row.style.display = text.indexOf(q) !== -1 ? '' : 'none';
      });
    });
  </script>
</body>
</html>
