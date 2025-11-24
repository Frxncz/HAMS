<?php
$host = "localhost";
$user = "root"; // default in XAMPP
$pass = "";     // leave blank unless you set a password
$db = "edoc";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
  die("Database connection failed: " . mysqli_connect_error());
}
?>
