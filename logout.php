<?php
// logout.php — Destroys the user session and redirects to the login page.
session_start();
$_SESSION = [];
session_destroy();
header('Location: login.php');
exit;
