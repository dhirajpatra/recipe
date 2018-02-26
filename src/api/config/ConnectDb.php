<?php

/**
 * Singleton Class ConnectDb
 */
class ConnectDb {
    // Hold the class instance.
    private static $instance = null;
    private $conn;

    /**
     * ConnectDb constructor. The db connection is established in the private constructor.
     */
    private function __construct()
    {
        $this->conn = null;
        
        try {
            $hostAndDb = 'pgsql:host='.POSTGRES_HOST.';port=5432;dbname='.POSTGRES_DB.'';
            $this->conn = new PDO($hostAndDb,POSTGRES_USER, POSTGRES_PASSWORD);
            $this->conn->exec("SET NAMES UTF8");

        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }

    }

    /**
     * @return ConnectDb|null
     */
    public static function getInstance()
    {
        if(!self::$instance)
        {
            self::$instance = new ConnectDb();
        }

        return self::$instance;
    }

    /**
     * @return PDO
     */
    public function getConnection()
    {
        return $this->conn;
    }
}