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
          <button class="btn primary">+ Add New Doctor</button>
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
            <tr>
              <td>Dr. Sarah Johnson</td>
              <td>sarah.johnson@hospital.com</td>
              <td>Cardiologist</td>
              <td>+1 (555) 123-4567</td>
              <td>45</td>
              <td><span class="status active">active</span></td>
              <td>
                <div class="actions">
                  <button class="btn view">View</button>
                  <button class="btn edit">Edit</button>
                  <button class="btn deactivate">Deactivate</button>
                </div>
              </td>
            </tr>
            <tr>
              <td>Dr. Michael Chen</td>
              <td>michael.chen@hospital.com</td>
              <td>General Practitioner</td>
              <td>+1 (555) 234-5678</td>
              <td>67</td>
              <td><span class="status active">active</span></td>
              <td>
                <div class="actions">
                  <button class="btn view">View</button>
                  <button class="btn edit">Edit</button>
                  <button class="btn deactivate">Deactivate</button>
                </div>
              </td>
            </tr>
            <tr>
              <td>Dr. Emily Williams</td>
              <td>emily.williams@hospital.com</td>
              <td>Pediatrician</td>
              <td>+1 (555) 345-6789</td>
              <td>34</td>
              <td><span class="status inactive">inactive</span></td>
              <td>
                <div class="actions">
                  <button class="btn view">View</button>
                  <button class="btn edit">Edit</button>
                  <button class="btn activate">Activate</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
  </div>
</body>
</html>
