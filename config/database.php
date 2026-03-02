<?php
/**
 * Database Configuration
 * 
 * Handles the MySQL connection using PDO.
 * Credentials are stored in session after login.
 * PDO is used throughout to prevent SQL injection via prepared statements.
 */

class Database
{
    /** @var string MySQL host */
    private $host;

    /** @var string MySQL username */
    private $username;

    /** @var string MySQL password */
    private $password;

    /** @var string|null Optional database name */
    private $dbname;

    /** @var PDO|null Active PDO connection */
    private $conn;

    /**
     * Constructor – reads credentials from session or parameters.
     *
     * @param string      $host
     * @param string      $username
     * @param string      $password
     * @param string|null $dbname
     */
    public function __construct(string $host = 'localhost', string $username = 'root', string $password = '', ?string $dbname = null)
    {
        $this->host     = $host;
        $this->username = $username;
        $this->password = $password;
        $this->dbname   = $dbname;
        $this->conn     = null;
    }

    /**
     * Open a PDO connection.
     *
     * @return PDO
     * @throws PDOException
     */
    public function connect(): PDO
    {
        if ($this->conn === null) {
            $dsn = "mysql:host={$this->host}";
            if ($this->dbname) {
                $dsn .= ";dbname={$this->dbname}";
            }
            $dsn .= ";charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        }

        return $this->conn;
    }

    /**
     * Close the connection.
     */
    public function disconnect(): void
    {
        $this->conn = null;
    }
}
