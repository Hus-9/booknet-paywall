<?php
// This links to the login controller so the user can login on this page
require_once("logincontroller.php");
// This creates a new stdClass object to store any data that's passed to the view
$view = new stdClass();
// Sets the title for the page
$view->pageTitle = 'Homepage';
// This loads the view file for the homepage
require_once('Views/index.phtml');