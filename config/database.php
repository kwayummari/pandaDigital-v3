<?php

require_once 'Environment.php';

class Database
{
    private static $instance = null;
    private $conn;

    private function __construct()
    {
        // Private constructor to prevent direct instantiation
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        // Return existing connection if available and valid
        if ($this->conn !== null) {
            try {
                // Test the connection
                $this->conn->query('SELECT 1');
                return $this->conn;
            } catch (PDOException $e) {
                // Connection is stale, reset it
                $this->conn = null;
            }
        }

        // Load environment configuration
        Environment::load();
        $config = Environment::getDatabaseConfig();

        $maxRetries = 3;
        $retryDelay = 1; // seconds

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $this->conn = new PDO(
                    "mysql:host={$config['host']};dbname={$config['name']};charset={$config['charset']}",
                    $config['user'],
                    $config['password'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$config['charset']}",
                        PDO::ATTR_PERSISTENT => true, // Enable persistent connections
                        PDO::ATTR_TIMEOUT => 30 // Connection timeout
                    ]
                );

                // Test the connection
                $this->conn->query('SELECT 1');
                return $this->conn;
            } catch (PDOException $exception) {
                error_log("Database connection attempt $attempt failed: " . $exception->getMessage());

                if ($attempt < $maxRetries) {
                    sleep($retryDelay);
                    $retryDelay *= 2; // Exponential backoff
                } else {
                    if (Environment::isDebug()) {
                        echo "Connection error: " . $exception->getMessage();
                    } else {
                        error_log("Database connection failed after $maxRetries attempts: " . $exception->getMessage());
                        echo "Database connection failed. Please try again later.";
                    }
                    return null;
                }
            }
        }

        return null;
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

    /**
     * Close database connection
     */
    public function closeConnection()
    {
        $this->conn = null;
    }

    /**
     * Prevent cloning of the instance
     */
    private function __clone() {}

    /**
     * Prevent unserialization of the instance
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}
