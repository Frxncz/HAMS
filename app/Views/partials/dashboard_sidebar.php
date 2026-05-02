<?php
if (!function_exists('render_dashboard_sidebar')) {
  function render_dashboard_sidebar(array $config): void
  {
    $role = $config['role'] ?? 'patient';
    $active = $config['active'] ?? '';
    $name = $config['name'] ?? '';
    $avatar = $config['avatar'] ?? strtoupper(substr((string) $name, 0, 2));
    $logoutHref = $config['logoutHref'] ?? '/HAMS/public/actions/auth/logout.php';
    $iconBase = $config['iconBase'] ?? '/HAMS/public/assets/icons/';

    if ($role === 'admin') {
      $menu = [
        ['key' => 'dashboard', 'href' => './admin_dashboard.php', 'icon' => 'adminDashboard/adminDashboard.svg', 'alt' => 'home icon', 'label' => 'Dashboard'],
        ['key' => 'viewDoctors', 'href' => './admin_viewDoctors.php', 'icon' => 'adminDashboard/viewDoctors.svg', 'alt' => 'doctor icon', 'label' => 'View Doctors'],
        ['key' => 'viewPatients', 'href' => './admin_viewPatients.php', 'icon' => 'adminDashboard/viewPatients.svg', 'alt' => 'consultations icon', 'label' => 'View Patients'],
        ['key' => 'appointments', 'href' => './admin_appointments.php', 'icon' => 'adminDashboard/appointments.svg', 'alt' => 'history icon', 'label' => 'Appointments'],
      ];
    } elseif ($role === 'doctor') {
      $menu = [
        ['key' => 'dashboard', 'href' => './doctor_dashboard.php', 'icon' => 'patientsDashboard/Home.svg', 'alt' => 'home icon', 'label' => 'Dashboard'],
        ['key' => 'myAppointments', 'href' => './doctor_myAppointments.php', 'icon' => 'patientsDashboard/findDoctor.svg', 'alt' => 'appointments icon', 'label' => 'My Appointments'],
        ['key' => 'mySessions', 'href' => './doctor_mySessions.php', 'icon' => 'patientsDashboard/myConsultation.svg', 'alt' => 'sessions icon', 'label' => 'My Sessions'],
        ['key' => 'bookingHistory', 'href' => './doctor_bookingHistory.php', 'icon' => 'patientsDashboard/bookingHistory.svg', 'alt' => 'history icon', 'label' => 'Booking History'],
        ['key' => 'settings', 'href' => './doctors_settings.php', 'icon' => 'patientsDashboard/setting.svg', 'alt' => 'settings icon', 'label' => 'Account Settings'],
      ];
    } else {
      $menu = [
        ['key' => 'dashboard', 'href' => './patient_dashboard.php', 'icon' => 'patientsDashboard/Home.svg', 'alt' => 'home icon', 'label' => 'Home'],
        ['key' => 'findDoctor', 'href' => './patient_find-doctor.php', 'icon' => 'patientsDashboard/findDoctor.svg', 'alt' => 'doctor icon', 'label' => 'Find Doctor'],
        ['key' => 'myConsultation', 'href' => './patient_myConsultation.php', 'icon' => 'patientsDashboard/myConsultation.svg', 'alt' => 'consultations icon', 'label' => 'My Consultations'],
        ['key' => 'bookingHistory', 'href' => './patient_bookingHistory.php', 'icon' => 'patientsDashboard/bookingHistory.svg', 'alt' => 'history icon', 'label' => 'Booking History'],
        ['key' => 'settings', 'href' => './patient_settings.php', 'icon' => 'patientsDashboard/setting.svg', 'alt' => 'settings icon', 'label' => 'Account Settings'],
      ];
    }
    ?>
    <aside class="sidebar">
      <div class="profile">
        <div class="avatar"><?php echo htmlspecialchars((string) $avatar); ?></div>
        <h2><?php echo htmlspecialchars((string) $name); ?></h2>
      </div>

      <button class="logout-btn" onclick="window.location.href='<?php echo htmlspecialchars($logoutHref); ?>'">
        <img src="<?php echo htmlspecialchars($iconBase . 'patientsDashboard/logout.svg'); ?>" alt="logout icon">
        Log out
      </button>

      <nav class="menu">
        <?php foreach ($menu as $item): ?>
          <a href="<?php echo htmlspecialchars($item['href']); ?>" class="menu-item<?php echo $active === $item['key'] ? ' active' : ''; ?>">
            <img src="<?php echo htmlspecialchars($iconBase . $item['icon']); ?>" alt="<?php echo htmlspecialchars($item['alt']); ?>">
            <?php echo htmlspecialchars($item['label']); ?>
          </a>
        <?php endforeach; ?>
      </nav>
    </aside>
    <?php
  }
}



