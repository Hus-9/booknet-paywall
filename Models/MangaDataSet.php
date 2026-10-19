<?php

require_once('manga.php');
require_once('Database.php');
class MangaDataSet{
    protected $_dbHandle, $_dbInstance;
    public function __construct() {
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
    }

    public function fetchAllMangas() {
        $sqlquery = 'SELECT * FROM manga';

        $stmt = $this->_dbHandle->prepare($sqlquery);
        $stmt->execute();

        $dataSet = [];
        //loop through and read the results and cast
        // them into a matching object
        while ($row = $stmt->fetch()) {
            $dataSet[] = new MangaClass($row);
        }
        return $dataSet;
    }
}
require_once ('MangaClass.php');