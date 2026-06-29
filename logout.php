<?php
/**
 * Logout Handler (SECURED)
 * Ministry of Health and Population - Nepal
 *
 * Security notes:
 * 1. Fully clears session array.
 * 2. Invalidates session cookie.
 * 3. Destroys session server-side before redirect.
 */

session_start();

// Destroy all session data
$_SESSION = array();

// Delete session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?>
