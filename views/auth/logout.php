<?php

require_once '../../config/Auth.php';
// auth/logout.php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

header("Location: login.php");
exit();
?>