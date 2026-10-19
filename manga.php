<?php
require_once ('Models/MangaDataSet.php');
require('sessionsecurity.php');
require_once ('permissions.php');
requireRoles(['BookNet Pro', 'Graphical'], 'paywall.php');

$view = new stdClass();
$view->pageTitle = "Manga";

$manga = new MangaDataSet();
$view->manga = $manga->fetchAllMangas();

if(count($view->manga)== 0) {
    $view->dbMessage = 'No Books found';
}
else{
    $view->dbMessage =  "Here are all the Books! There are ".count($view->manga) ."result(s)";
}

require_once('Views/manga.phtml');