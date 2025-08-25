<?php
/**
 * Admin Logout
 * Destroys session and redirects to login page
 */

// Include functions
require_once __DIR__ . '/../includes/functions.php';

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destroy all session data
session_unset();
session_destroy();

// Clear session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Redirect to login page with logout message
header('Location: login.php?msg=logged_out');
exit();
?>