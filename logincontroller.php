<?php
require_once __DIR__ . '/Models/Database.php';

// Ensure the session ends when the browser is closed
ini_set('session.cookie_lifetime', 0);
session_start();

// Login function
if (isset($_POST["loginbutton"])) {
    // Trim removes any extra spaces from the username inputted
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    try {
        // Creates a new connection to the db
        try {
            // DB is in the /database subfolder
            $db = Database::getConnection();
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection error: " . htmlspecialchars($e->getMessage()));
        }

        // A prepared statement with a bound parameter to prevent SQL injection
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]); // Executes the query with the username
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifies the login input (SECURE: using password_verify)
        if ($user && password_verify($password, $user["password_hash"])) {

            // Store basic login info
            $_SESSION["login"] = $user['username'];      // use value from DB
            $_SESSION['user_id'] = $user['id'];

            // Work out the role from the DB
            if (isset($user['role']) && $user['role'] !== '') {
                $role = $user['role'];
            } else {
                $role = 'Free'; // default if no role stored
            }

            // Normalise any old 'user' values from the DB/session to 'Free'
            // so it never shows as 'user' in the UI
            if (strcasecmp($role, 'user') === 0) {
                $role = 'Free';
            }

            // Save the final role into the session
            $_SESSION['role'] = $role;

            // Nice message (optional)
            $_SESSION["message"] = "You are logged in as " . htmlspecialchars($user['username']);

        } else {
            // Displays an error message if the login fails
            $_SESSION["message"] = "Error in username or password";
        }

    } catch (PDOException $e) {
        // Display a database error message (escaped for security)
        echo "Database error: " . htmlspecialchars($e->getMessage());
    }
}

// Logout function
if (isset($_POST["logoutbutton"])) {
    // Sets a logout confirmation message
    $_SESSION["message"] = "User is logged out";

    // Clean up session variables
    unset($_SESSION["login"]);
    unset($_SESSION["role"]);
    unset($_SESSION["user_id"]);

    // Ends the session
    session_destroy();
}