<?php
class AdminData {
    // Private fields needed for sightings
    protected  $_role, $_username;
    // constructor
    public function __construct($dbRow) {
        $this->_role = $dbRow['role'];
        $this->_username = $dbRow['username'];
    }
    // Accessor methods for the fields
    public function getRole() {
        return $this->_role;
    }
    public function getUsername() {
        return $this->_username;
    }


}