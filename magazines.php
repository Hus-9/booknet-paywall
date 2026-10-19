<?php
require_once ('Models/MagazinesDataSet.php');
require('sessionsecurity.php');
require_once ('permissions.php');
requireRoles(['Reader', 'BookNet Pro'], 'paywall.php');

$view = new stdClass();
$view->pageTitle = "Magazines";

$magazine = new MagazinesDataSet();
$view->magazine = $magazine->fetchAllMagazines();

if(count($view->magazine)== 0) {
    $view->dbMessage = 'No Magazines found';
}
else{
    $view->dbMessage =  "Here are all the Magazines! There are ".count($view->magazine) ." result(s)";
}

require_once('Views/magazines.phtml');