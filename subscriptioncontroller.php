<?php
require('sessionsecurity.php');

/**
 * BookNet Subscription Controller
 * Uses existing `role` column in `users` table.
 *
 * Expects:
 *   POST plan    = "Free", "Reader", "Graphical", "BookNet Pro"
 *   POST billing = "monthly" or "yearly" (optional)
 */

// 🔧 Adjust if your paywall URL is different
$paywallUrl = 'paywall.phtml';

// 1. User must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 2. Must be POST with a plan
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['plan'])) {
    header("Location: {$paywallUrl}?error=NoPlanSelected");
    exit;
}

$role    = trim($_POST['plan']);                     // value to store in `role`
$billing = isset($_POST['billing']) ? trim($_POST['billing']) : 'monthly';

$allowedRoles   = ['Free', 'Reader', 'Graphical', 'BookNet Pro'];
$allowedBilling = ['monthly', 'yearly'];

if (!in_array($role, $allowedRoles, true)) {
    header("Location: {$paywallUrl}?error=InvalidRole");
    exit;
}

if (!in_array($billing, $allowedBilling, true)) {
    $billing = 'monthly';
}

$userId = (int) $_SESSION['user_id'];

try {
    // ✅ IMPORTANT: this assumes this file is in the `audio` folder
    // and the DB is in `audio/database/BookNet.sqlite`
    $pdo = new PDO('sqlite:database/BookNet.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("
        UPDATE users
        SET role = :role
        WHERE id = :id
    ");

    $stmt->execute([
        ':role' => $role,
        ':id'   => $userId,
    ]);

    // Save to session too (handy for navbars etc.)
    $_SESSION['role']          = $role;
    $_SESSION['billing_cycle'] = $billing;

    // On success, redirect to home/account
    header("Location: index.php?subscription=updated");
    exit;

} catch (PDOException $e) {
    // 🔍 TEMPORARY DEBUG (uncomment to see actual DB error in the browser)
    /*
    echo "<h2>Database error</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</pre>";
    exit;
    */

    header("Location: {$paywallUrl}?error=Database");
    exit;
}