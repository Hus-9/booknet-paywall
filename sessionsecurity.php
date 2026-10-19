<?php
// Checks if a session is active
if (session_status() === PHP_SESSION_NONE) {
    // Makes sure the session ends when the browser is closed
    ini_set('session.cookie_lifetime', 0);
    // Starts the session to give access to the session variables
    session_start();
}

// Checks if the user is logged in
if (!isset($_SESSION["login"])) {
    // Sends the user to the login page if not already logged in
    header("Location: index.php");
    // Ends the script after the user is re-directed
    exit;
}