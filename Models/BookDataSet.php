<?php

require_once('books.php');
require_once('Database.php');
class BookDataSet{
    protected $_dbHandle, $_dbInstance;
    public function __construct() {
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
    }

    public function fetchAllBooks() {
        $sqlquery = 'SELECT * FROM books';

        $stmt = $this->_dbHandle->prepare($sqlquery);
        $stmt->execute();

        $dataSet = [];
        //loop through and read the results and cast
        // them into a matching object
        while ($row = $stmt->fetch()) {
            $dataSet[] = new BookClass($row);
        }
        return $dataSet;
    }
}
require_once('BookClass.php');