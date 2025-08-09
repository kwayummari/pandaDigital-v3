<?php

require_once 'Environment.php';

class Database
{
    private $conn;

    public function getConnection()
    {
        $this->conn = null;

        // Load environment configuration
        Environment::load();
        $config = Environment::getDatabaseConfig();

        try {
            $this->conn = new PDO(
                "mysql:host={$config['host']};dbname={$config['name']};charset={$config['charset']}",
                $config['user'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$config['charset']}"
                ]
            );
        } catch (PDOException $exception) {
            if (Environment::isDebug()) {
                echo "Connection error: " . $exception->getMessage();
            } else {
                error_log("Database connection error: " . $exception->getMessage());
                echo "Database connection failed. Please try again later.";
            }
        }

        return $this->conn;
    }

    /**
     * Test database connection
     */
    public function testConnection()
    {
        try {
            $conn = $this->getConnection();
            if ($conn) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
        return false;
    }

    /**
     * Get database configuration (for debugging)
     */
    public function getConfig()
    {
        Environment::load();
        return Environment::getDatabaseConfig();
    }
}
