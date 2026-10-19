<?php
require_once('Models/BookDataSet.php');

$view = new stdClass();
$view->pageTitle = "Books";

$books = new BookDataSet();
$view->books = $books->fetchAllBooks();

if (count($view->books) == 0) {
    $view->dbMessage = 'No Books found';
} else {
    $view->dbMessage = "Here are all the Books! There are ".count($view->books)." result(s)";
}

require_once('Views/books.phtml');