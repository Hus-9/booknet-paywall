<?php
// Ensure the session ends when the browser is closed
ini_set('session.cookie_lifetime', 0);
session_start();

if (isset($_POST["signupbutton"])) {
    // Collect and sanitize inputs
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    try {
        try {
            // DB is in the /database subfolder
            $dbPath = __DIR__ . '/database/BookNet.sqlite';

            $db = new PDO('sqlite:' . $dbPath);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection error: " . htmlspecialchars($e->getMessage()));
        }

        // Hash the password securely
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user (only username, email, password_hash)
        $stmt = $db->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $passwordHash]);

        $_SESSION["message"] = "Signup successful! You can now log in.";

        header("Location: index.php");
        exit;

    } catch (PDOException $e) {
        // Log error internally, show generic message
        error_log($e->getMessage());
        $_SESSION["message"] = "Internal error. Please try again later.";
    }
}