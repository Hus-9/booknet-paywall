<?php

require_once('magazines.php');
require_once('Database.php');
class MagazinesDataSet{
    protected $_dbHandle, $_dbInstance;
    public function __construct() {
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
    }

    public function fetchAllMagazines() {
        $sqlquery = 'SELECT * FROM magazines';

        $stmt = $this->_dbHandle->prepare($sqlquery);
        $stmt->execute();

        $dataSet = [];
        //loop through and read the results and cast
        // them into a matching object
        while ($row = $stmt->fetch()) {
            $dataSet[] = new MagazinesClass($row);
        }
        return $dataSet;
    }
}
require_once ('MagazinesClass.php');