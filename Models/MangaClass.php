<?php

class MangaClass{

    //private fields
    protected $_id , $_title , $_author , $_genre , $_publisher , $_yearPublished, $_cover;

    //constructor
    public function __construct($dbrow) {
        $this->_id = $dbrow['id'];
        $this->_title = $dbrow['title'];
        $this->_author = $dbrow['author'];
        $this->_genre = $dbrow['genre'];
        $this->_publisher = $dbrow['publisher'];
        $this->_yearPublished = $dbrow['year_published'];
        $this->_cover = $dbrow['cover'];
    }

    public function getID() {
        return $this->_id;
    }
    public function getTitle() {

        return $this->_title;
    }
    public function getAuthor() {
        return $this->_author;
    }
    public function getGenre() {
        return $this->_genre;
    }
    public function getPublisher() {
        return $this->_publisher;
    }
    public function getYearPublished() {
        return $this->_yearPublished;
    }
    public function getCover() {
        return $this->_cover;
    }

}
