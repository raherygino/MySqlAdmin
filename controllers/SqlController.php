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
$statementResults = [];
$executionTime = null;
$errorMsg    = null;

try {
    // Connect to specific database if provided, otherwise server-level
    $pdo = $dbName ? get_connection($dbName) : get_connection();

    // Get list of databases for the dropdown
    $databases = $pdo->query("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME")->fetchAll(PDO::FETCH_COLUMN);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($sql)) {
        $startTime = microtime(true);

        // Normalize input: remove BOM and trim
        $sqlText = preg_replace('/^\xEF\xBB\xBF/', '', $sql);
        $sqlText = trim($sqlText);

        // Split into statements (simple splitting; ignores empty statements)
        $parts = preg_split('/;\s*(\r?\n|$)/', $sqlText);
        $statements = [];
        foreach ($parts as $part) {
            $s = trim($part);
            if ($s !== '') {
                $statements[] = $s;
            }
        }

        // If no semicolons were used, preg_split may still return the whole string
        if (empty($statements) && $sqlText !== '') {
            $statements = [$sqlText];
        }

        foreach ($statements as $stmtSql) {
            $trimmedSql = ltrim($stmtSql);
            $isResultSet = preg_match('/^\s*(SELECT|SHOW|DESCRIBE|DESC|EXPLAIN)\b/i', $trimmedSql);

            if ($isResultSet) {
                $stmt = $pdo->query($stmtSql);
                $rows = $stmt->fetchAll();
                $cols = !empty($rows) ? array_keys($rows[0]) : [];
                $statementResults[] = [
                    'sql' => $stmtSql,
                    'type' => 'resultset',
                    'columns' => $cols,
                    'rows' => $rows,
                    'row_count' => count($rows),
                ];
            } else {
                $count = $pdo->exec($stmtSql);
                $statementResults[] = [
                    'sql' => $stmtSql,
                    'type' => 'affected',
                    'affected_rows' => ($count === false ? 0 : (int)$count),
                ];
            }
        }

        // Backward-compatible fields for the existing view (first statement only)
        if (!empty($statementResults)) {
            $first = $statementResults[0];
            if ($first['type'] === 'resultset') {
                $results = $first['rows'];
                $columns = $first['columns'];
            } else {
                $affectedRows = $first['affected_rows'];
            }
        }

        $executionTime = round((microtime(true) - $startTime) * 1000, 2);
    }

} catch (PDOException $e) {
    $errorMsg = $e->getMessage();
} catch (Exception $e) {
    $errorMsg = $e->getMessage();
}

require_once __DIR__ . '/../views/sql/editor.php';
