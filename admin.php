<?php
// Start session if not already started
require('sessionsecurity.php');
require_once ('permissions.php');
requireRoles(['Admin'], 'index.php');
require_once('Models/AdminDataSet.php');

$view = new stdClass();
$view->pageTitle = 'Admin Dashboard';

// Fetch all admin users
$adminDataSet = new AdminDataSet();
$view->admins = $adminDataSet->fetchAdmin();

require_once('Views/admin.phtml');
