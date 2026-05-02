<?php
session_start();
session_unset();
session_destroy();
header("Location: /HAMS/public/login.php");
exit();
?>

