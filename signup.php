<?php
// This links to the signup controller so the user can register on this page
require_once("signupcontroller.php");

// Create a new stdClass object to store any data passed to the view
$view = new stdClass();

// Set the title for the page
$view->pageTitle = 'Signup';

// Load the view file for the signup page
require_once('Views/signup.phtml');