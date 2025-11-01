<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Prevent page caching
header("Cache-Control: no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Redirect to login or home
header("Location: user_login.php");
exit();
?>
