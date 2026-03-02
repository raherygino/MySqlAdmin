<?php
/**
 * SQL Controller
 * 
 * Provides a custom SQL query editor.
 * Users can execute arbitrary SQL statements and view results.
 * Supports SELECT (returns result set) and other statements (returns affected rows).
 */

require_once __DIR__ . '/../helpers/functions.php';

$dbName = $_GET['db'] ?? $_POST['db'] ?? '';
$sql    = trim($_POST['sql'] ?? $_GET['sql'] ?? '');
$results     = null;
$columns     = [];
$affectedRows = null;
$executionTime = null;
$errorMsg    = null;

try {
    // Connect to specific database if provided, otherwise server-level
    $pdo = $dbName ? get_connection($dbName) : get_connection();

    // Get list of databases for the dropdown
    $databases = $pdo->query("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME")->fetchAll(PDO::FETCH_COLUMN);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($sql)) {
        $startTime = microtime(true);

        // Determine if the query is a SELECT-type (returns rows)
        $trimmedSql = ltrim($sql);
        $isSelect = preg_match('/^\s*(SELECT|SHOW|DESCRIBE|DESC|EXPLAIN)\b/i', $trimmedSql);

        if ($isSelect) {
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            if (!empty($results)) {
                $columns = array_keys($results[0]);
            }
        } else {
            $affectedRows = $pdo->exec($sql);
        }

        $executionTime = round((microtime(true) - $startTime) * 1000, 2);
    }

} catch (PDOException $e) {
    $errorMsg = $e->getMessage();
} catch (Exception $e) {
    $errorMsg = $e->getMessage();
}

require_once __DIR__ . '/../views/sql/editor.php';
