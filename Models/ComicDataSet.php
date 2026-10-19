<?php

require_once('comics.php');
require_once('Database.php');
class ComicDataSet{
    protected $_dbHandle, $_dbInstance;
    public function __construct() {
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
    }

    public function fetchAllComics() {
        $sqlquery = 'SELECT * FROM comics';

        $stmt = $this->_dbHandle->prepare($sqlquery);
        $stmt->execute();

        $dataSet = [];
        //loop through and read the results and cast
        // them into a matching object
        while ($row = $stmt->fetch()) {
            $dataSet[] = new ComicData($row);
        }
        return $dataSet;
    }
}
require_once ('ComicData.php');