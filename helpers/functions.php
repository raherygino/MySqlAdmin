<?php
/**
 * Helper Functions
 * 
 * Utility functions used across the application:
 * - Session management
 * - Input sanitization
 * - Flash messages
 * - Database connection factory
 */

/**
 * Start session if not already started.
 */
function init_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Check if the user is authenticated.
 *
 * @return bool
 */
function is_logged_in(): bool
{
    init_session();
    return isset($_SESSION['db_user']);
}

/**
 * Require authentication – redirect to login if not authenticated.
 */
function require_login(): void
{
    if (!is_logged_in()) {
        redirect('index.php?page=login');
    }
}

/**
 * Get a PDO connection using session credentials.
 *
 * @param string|null $dbname Optional database name
 * @return PDO
 */
function get_connection(?string $dbname = null): PDO
{
    require_once __DIR__ . '/../config/database.php';

    $host = $_SESSION['db_host'] ?? 'localhost';
    $user = $_SESSION['db_user'] ?? 'root';
    $pass = $_SESSION['db_pass'] ?? '';

    $db = new Database($host, $user, $pass, $dbname);
    return $db->connect();
}

/**
 * Redirect to a URL and exit.
 *
 * @param string $url
 */
function redirect(string $url): void
{
    header("Location: $url");
    exit;
}

/**
 * Set a flash message in session.
 *
 * @param string $type    success|error|warning|info
 * @param string $message
 */
function set_flash(string $type, string $message): void
{
    init_session();
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Retrieve and clear the flash message.
 *
 * @return array|null
 */
function get_flash(): ?array
{
    init_session();
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Escape output for HTML to prevent XSS.
 *
 * @param mixed $value
 * @return string
 */
function h($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * Get a request parameter safely.
 *
 * @param string $key
 * @param mixed  $default
 * @return mixed
 */
function input(string $key, $default = '')
{
    return $_REQUEST[$key] ?? $default;
}

/**
 * Get the current page from the query string.
 *
 * @return string
 */
function current_page(): string
{
    return $_GET['page'] ?? 'dashboard';
}

/**
 * Build a URL with query parameters.
 *
 * @param array $params
 * @return string
 */
function build_url(array $params): string
{
    return 'index.php?' . http_build_query($params);
}

/**
 * Format bytes into human-readable size.
 *
 * @param int $bytes
 * @return string
 */
function format_bytes(int $bytes): string
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}

/**
 * Get MySQL data types for column creation forms.
 *
 * @return array
 */
function get_mysql_types(): array
{
    return [
        'Numeric'  => ['INT', 'TINYINT', 'SMALLINT', 'MEDIUMINT', 'BIGINT', 'DECIMAL', 'FLOAT', 'DOUBLE', 'BIT'],
        'String'   => ['VARCHAR', 'CHAR', 'TEXT', 'TINYTEXT', 'MEDIUMTEXT', 'LONGTEXT', 'BLOB', 'ENUM', 'SET'],
        'Date'     => ['DATE', 'DATETIME', 'TIMESTAMP', 'TIME', 'YEAR'],
    ];
}
