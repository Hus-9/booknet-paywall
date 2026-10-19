<?php
class ComicData {
    // Private fields needed for sightings
    protected $_id, $_title, $_author, $_genre, $_publisher, $_yearPublished, $_cover;
    // constructor
    public function __construct($dbRow) {
        $this->_id = $dbRow['id'];
        $this->_title = $dbRow['title'];
        $this->_author = $dbRow['author'];
        $this->_genre = $dbRow['genre'];
        $this->_publisher = $dbRow['publisher'];
        $this->_yearPublished = $dbRow['year_published'];
        $this->_cover = $dbRow['cover'];
    }
    // Accessor methods for the fields
    public function getID() {
        return $this->_id;
    }
    public function getComicTitle() {
        return $this->_title;
    }
    public function getComicAuthor() {
        return $this->_author;
    }
    public function getComicGenre() {
        return $this->_genre;
    }
    public function getComicPublisher() {
        return $this->_publisher;
    }
    public function getComicCover() {
        return $this->_cover;
    }
    public function getComicYearPublished() {
        return $this->_yearPublished;
    }
}