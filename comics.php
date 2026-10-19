<?php
require_once ('Models/ComicDataSet.php');
require('sessionsecurity.php');
require_once ('permissions.php');
requireRoles(['BookNet Pro', 'Graphical'], 'paywall.php');

$view =new stdClass();
$view->pageTitle = "Comic";

$comic = new ComicDataSet();
$view->comic = $comic->fetchAllComics();

if(count($view->comic)== 0) {
    $view->dbMessage = 'No Comics found';
}
else{
    $view->dbMessage =  "Here are all the Comics! There are ".count($view->comic) ."result(s)";
}

require_once('Views/comics.phtml');