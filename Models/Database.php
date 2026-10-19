<?php

class Database
{
    /**
     * Holds a single instance of this class.
     *
     * @var Database|null
     */
    protected static $_dbInstance = null;

    /**
     * This has the database connection (PDO).
     *
     * @var PDO|null
     */
    protected $_dbHandle = null;

    /**
     * Legacy-style singleton: returns the Database instance.
     * Usage in existing code: Database::getInstance()->getdbConnection()
     *
     * @return Database
     */
    public static function getInstance()
    {
        if (self::$_dbInstance === null) {
            self::$_dbInstance = new self();
        }
        return self::$_dbInstance;
    }

    /**
     * New-style helper: returns the PDO connection directly.
     * Usage: $db = Database::getConnection();
     *
     * @return PDO
     */
    public static function getConnection()
    {
        return self::getInstance()->getdbConnection();
    }

    /**
     * Private constructor so it can't be called outside this class.
     */
    private function __construct()
    {
        // Correct path relative to /Models
        // __DIR__ is the folder containing this file (likely /Models)
        $dbPath = __DIR__ . '/../database/BookNet.sqlite';

        try {
            $this->_dbHandle = new PDO('sqlite:' . $dbPath);
            $this->_dbHandle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->_dbHandle->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Basic error handling to avoid fatal "uncaught exception"
            die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
        }
    }

    /**
     * Returns the PDO connection.
     *
     * @return PDO
     */
    public function getdbConnection()
    {
        if ($this->_dbHandle === null) {
            // If for some reason the connection disappeared
            throw new RuntimeException('Database connection is not available.');
        }
        return $this->_dbHandle;
    }

    /**
     * Optional cleanup.
     */
    public function __destruct()
    {
        $this->_dbHandle = null;
    }
}