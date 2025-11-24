<?php
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST["email"];
  $password = $_POST["password"];

  $query = "SELECT * FROM webuser WHERE email='$email'";
  $result = mysqli_query($conn, $query);

  if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $usertype = $row["usertype"];

    if ($usertype == 'a') {
      $check = mysqli_query($conn, "SELECT * FROM admin WHERE aemail='$email' AND apassword='$password'");
      if (mysqli_num_rows($check) == 1) {
        header("Location: ../admin-dashboard.php");
        exit();
      }
    }

    if ($usertype == 'd') {
      $check = mysqli_query($conn, "SELECT * FROM doctor WHERE docemail='$email' AND docpassword='$password'");
      if (mysqli_num_rows($check) == 1) {
        header("Location: ../doctor-dashboard.php");
        exit();
      }
    }

    if ($usertype == 'p') {
      $check = mysqli_query($conn, "SELECT * FROM patient WHERE pemail='$email' AND ppassword='$password'");
      if (mysqli_num_rows($check) == 1) {
        header("Location: ../patient-dashboard.php");
        exit();
      }
    }
  }

  echo "<script>alert('Invalid email or password!'); window.location='../login.php';</script>";
}
?>
