<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
// Unset specific session variables
unset($_SESSION['loggedin']);
unset($_SESSION['usertype']);
unset($_SESSION['user_id']);

// Destroy the session
session_destroy();

// Redirect to login page after logout
header('Location: userlogin');
exit();
?>