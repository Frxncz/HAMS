<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'patient') {
  header("Location: ../../login.html");
  exit();
}

include('../../backend/db_connect.php');

$patient_name = $_SESSION['name'];
$patient_id = $_SESSION['user_id'];

// Load doctors for dropdown
$doctors = [];
$result = $conn->query("SELECT docid, name FROM doctor ORDER BY name ASC");
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) $doctors[] = $row;
} else {
  // fallback list if no doctors yet
  $doctors = [
    ["docid" => 1, "name" => "Dr. John Doe"],
    ["docid" => 2, "name" => "Dr. Sarah Cruz"]
  ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Schedule Appointment</title>
  <link rel="stylesheet" href="../../css/global.css" />
  <style>
    body { font-family: Inter, sans-serif; background:#f9fbfd; display:flex; justify-content:center; align-items:center; height:100vh; }
    .form-container { background:white; padding:30px; border-radius:10px; width:400px; box-shadow:0 4px 20px rgba(0,0,0,0.1); }
    h2 { margin-bottom:20px; text-align:center; color:#3a6b97; }
    label { font-size:14px; display:block; margin-bottom:6px; }
    select, input { width:100%; padding:8px; margin-bottom:15px; border:1px solid #ccc; border-radius:6px; }
    button { padding:10px 20px; border:none; border-radius:6px; cursor:pointer; font-weight:600; }
    .primary { background:#3a6b97; color:white; }
    .ghost { background:#eaeaea; }
    .actions { display:flex; justify-content:space-between; }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Schedule Appointment</h2>
    <form action="../../backend/create_appointment.php" method="POST">
      <label for="doctor_id">Doctor</label>
      <select name="doctor_id" required>
        <option value="">-- Select Doctor --</option>
        <?php foreach ($doctors as $d): ?>
          <option value="<?php echo $d['docid']; ?>"><?php echo htmlspecialchars($d['name']); ?></option>
        <?php endforeach; ?>
      </select>

      <label for="appt_date">Date</label>
      <input type="date" name="appt_date" required min="<?php echo date('Y-m-d'); ?>">

      <label for="appt_time">Time</label>
      <input type="time" name="appt_time" required>

      <label for="purpose">Purpose of Visit</label>
      <select name="purpose" required>
        <option value="">-- Select purpose --</option>
        <option value="regular">Regular</option>
        <option value="new_patient">New Patient</option>
        <option value="follow_up">Follow Up</option>
      </select>

      <div class="actions">
        <button type="button" class="ghost" onclick="window.location.href='patient_dashboard.php'">Cancel</button>
        <button type="submit" class="primary">Submit</button>
      </div>
    </form>
  </div>
</body>
</html>
