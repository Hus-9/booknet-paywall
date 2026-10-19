<?php

class BookClass
{
    // private fields
    protected $_id;
    protected $_title;
    protected $_author;
    protected $_genre;
    protected $_publisher;
    protected $_yearPublished;
    protected $_cover;

    // constructor
    public function __construct($dbrow)
    {
        $this->_id            = $dbrow['id'] ?? null;
        $this->_title         = $dbrow['title'] ?? '';
        $this->_author        = $dbrow['author'] ?? '';
        $this->_genre         = $dbrow['genre'] ?? '';
        $this->_publisher     = $dbrow['publisher'] ?? '';
        $this->_yearPublished = $dbrow['year_published'] ?? '';

        // 🔍 Cover field – MUST exist
        if (isset($dbrow['cover'])) {
            $this->_cover = $dbrow['cover'];
        } elseif (isset($dbrow['Cover'])) {
            $this->_cover = $dbrow['Cover'];
        } else {
            // TEMP DEBUG: show what keys this row actually has
            echo '<pre>BookClass: no "cover" column in row. Row is:' . PHP_EOL;
            var_dump($dbrow);
            echo '</pre>';
            exit; // stop so you can see the problem clearly
        }
    }

    public function getID()            { return $this->_id; }
    public function getTitle()         { return $this->_title; }
    public function getAuthor()        { return $this->_author; }
    public function getGenre()         { return $this->_genre; }
    public function getPublisher()     { return $this->_publisher; }
    public function getYearPublished() { return $this->_yearPublished; }
    public function getCover()         { return $this->_cover; }
}