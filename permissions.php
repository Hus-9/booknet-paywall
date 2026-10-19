<?php
/**
 * Require the user to be logged in and have one of the allowed roles.
 *
 * @param array $allowedRoles List of role names that can access the page
 * @param string $redirectPage Where to send the user if they don't have access (optional)
 */
function requireRoles(array $allowedRoles, string $redirectPage = 'permissions.php'): void
{
    // Make sure the session is started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Must be logged in
    if (empty($_SESSION["login"])) {
        header("Location: index.php");
        exit;
    }

    // Get current role from session, default to "Free"
    $currentRoleRaw = $_SESSION['role'] ?? 'Free';

    // Normalise (remove spaces, lower-case for comparison)
    $currentRole = strtolower(trim($currentRoleRaw));

    // Normalise all allowed roles the same way
    $normalizedAllowed = array_map(
            static fn($role) => strtolower(trim($role)),
            $allowedRoles
    );

    // If their role is NOT allowed → show custom modal and redirect
    if (!in_array($currentRole, $normalizedAllowed, true)) {

        $_SESSION["message"] = "You do not have access to that page with your current subscription.";

        // (your styled modal, unchanged)
        echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Access denied – BookNet</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: radial-gradient(circle at top, #1d4ed8 0, #020617 45%, #000 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e5e7eb;
        }

        .permission-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.60);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 999;
            backdrop-filter: blur(4px);
        }

        .permission-modal {
            background: #020617;
            border-radius: 18px;
            padding: 24px 26px 20px;
            max-width: 420px;
            width: 90%;
            box-shadow: 0 22px 45px rgba(15, 23, 42, 0.85);
            border: 1px solid rgba(59, 130, 246, 0.5);
            text-align: left;
            animation: fadeInScale 0.22s ease-out;
        }

        .permission-modal-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .permission-modal-icon {
            width: 32px;
            height: 32px;
            border-radius: 999px;
            background: radial-gradient(circle at 30% 0%, #f97316 0, #fb923c 40%, #7c2d12 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 0.9rem;
            box-shadow: 0 0 18px rgba(249, 115, 22, 0.7);
        }

        .permission-modal h2 {
            margin: 0;
            font-size: 1.2rem;
            color: #f9fafb;
        }

        .permission-modal p {
            margin: 6px 0 16px;
            color: #9ca3af;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .permission-modal-highlight {
            color: #e5e7eb;
            font-weight: 500;
        }

        .permission-modal-actions {
            margin-top: 6px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .permission-modal button {
            display: block;
            width: 100%;
            padding: 10px 18px;
            border-radius: 999px;
            border: none;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease, color 0.15s ease;
        }

        .permission-modal button:focus-visible {
            outline: 2px solid #93c5fd;
            outline-offset: 2px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            color: #f9fafb;
            box-shadow: 0 12px 22px rgba(37, 99, 235, 0.45);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8, #4338ca);
            transform: translateY(-1px);
            box-shadow: 0 18px 30px rgba(37, 99, 235, 0.55);
        }

        .btn-ghost {
            background: transparent;
            color: #e5e7eb;
            border: 1px solid rgba(148, 163, 184, 0.5);
        }

        .btn-ghost:hover {
            background: rgba(15, 23, 42, 0.95);
            border-color: rgba(148, 163, 184, 0.9);
            transform: translateY(-1px);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.85);
        }

        .permission-modal small {
            display: block;
            margin-top: 10px;
            font-size: 0.78rem;
            color: #6b7280;
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.94);
            }
            to {
                opacity: 1;
            }
        }
    </style>
</head>
<body>
<div class="permission-overlay">
    <div class="permission-modal">
        <div class="permission-modal-header">
            <div class="permission-modal-icon">!</div>
            <h2>Access denied</h2>
        </div>
        <p>
            You do not have permission to view this page with your current
            <span class="permission-modal-highlight">BookNet subscription level</span>.
        </p>
        <div class="permission-modal-actions">
            <button id="goPaywallBtn" class="btn-primary">
                Upgrade your subscription
            </button>
            <button id="goHomeBtn" class="btn-ghost">
                Return to homepage
            </button>
        </div>
        <small>You\'ll be redirected automatically in a few seconds.</small>
    </div>
</div>

<script>
    const goHomeBtn = document.getElementById("goHomeBtn");
    const goPaywallBtn = document.getElementById("goPaywallBtn");

    function goHome() {
        window.location.href = "index.php";
    }
    function goPaywall() {
        window.location.href = "paywall.php";
    }

    goHomeBtn.addEventListener("click", goHome);
    goPaywallBtn.addEventListener("click", goPaywall);

    // Auto-redirect after 4 seconds
    setTimeout(goHome, 4000);
</script>
</body>
</html>';

        exit;
    }
}