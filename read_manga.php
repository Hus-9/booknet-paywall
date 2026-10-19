<?php
// Controller: manga reader with role-based freemium pagination.
ini_set('session.cookie_lifetime', 0);
session_start();

require_once __DIR__ . '/Models/MangaPages.php';
require_once __DIR__ . '/Models/User.php';

// Sync role with DB if logged in; clear if not.
if (!empty($_SESSION['user_id'])) {
    $sessionUser = User::findById((int) $_SESSION['user_id']);

    if ($sessionUser) {
        if (!empty($sessionUser['role'])) {
            $_SESSION['role'] = $sessionUser['role'];
        } else {
            $_SESSION['role'] = 'Free';
        }
    } else {
        unset($_SESSION['user_id'], $_SESSION['user_email'], $_SESSION['role']);
    }
} else {
    unset($_SESSION['role']);
}

$mangaId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$page   = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

$loggedIn = !empty($_SESSION['user_id']);

$role = 'Free';
if (!empty($_SESSION['role'])) {
    $role = $_SESSION['role'];
}

// Reader & BookNet Pro have full access.
// Free & Graphical are limited to 5 pages.
$canReadAll = in_array($role, array('Graphical', 'BookNet Pro'));

$isPremium = $canReadAll ? 1 : 0;

$manga = $mangaId > 0 ? MangaPages::getMangaById($mangaId) : null;
if (!$manga) {
    http_response_code(404);
    echo 'Manga not found.';
    exit;
}

MangaPages::ensurePagesExist($manga);
$maxPage = MangaPages::getPageCount($mangaId);

if ($canReadAll) {
    $allowedMaxPage = $maxPage;
} else {
    $allowedMaxPage = ($maxPage < 5 ? $maxPage : 5);
}

$limited = false;
if ($page > $allowedMaxPage) {
    $page = $allowedMaxPage;
    $limited = true;
}

$content = MangaPages::getPageContent($mangaId, $page);

// Pass variables to the view
$view = new stdClass();
$view->pageTitle = 'Read Manga';

require_once('Views/read_manga.phtml');