<?php
// logout.php - log the user out and return to home
session_start();

// Clear all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Optional: also delete the session cookie, if you want to be extra safe
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Redirect back to home page
header('Location: index.php');
exit;
